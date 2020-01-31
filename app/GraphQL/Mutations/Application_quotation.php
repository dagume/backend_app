<?php

namespace App\GraphQL\Mutations;
use App\User;
use App\Order;
use App\Mail\RequestForQuotation;
use App\Quotation;
use App\Document_reference;
use Illuminate\Support\Facades\Mail;
use App\Repositories\OrderRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\DetailRepository;
use App\Repositories\Document_referenceRepository;
use App\Repositories\Order_documentRepository;
use App\Repositories\ContactRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;

class Application_quotation
{
    protected $orderRepo;
    protected $quotationRepo;
    protected $detailRepo;
    protected $documentRepo;
    protected $order_docRepo;
    protected $contactRepo;

    public function __construct(OrderRepository $ordRepo, QuotationRepository $quoRepo, DetailRepository $detRepo, Document_referenceRepository $docRepo, Order_documentRepository $ordocRepo, ContactRepository $conRepo)
    {
        $this->quotationRepo = $quoRepo;
        $this->orderRepo = $ordRepo;
        $this->detailRepo = $detRepo;
        $this->documentRepo = $docRepo;
        $this->order_docRepo = $ordocRepo;
        $this->contactRepo = $conRepo;
    }
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        
        $ord = DB::transaction(function () use($args){  //se crea la transacion
            $args['application_date']   = now();
            $args['state']              = 0; //el valor 0 es el estado de Application
            $args['sender_data']        = auth()->user()->id;
            $order = $this->orderRepo->create($args);   //creamos la nueva orden

            foreach ($args['updetails'] as $arg) {
                $arg['order_id'] = $order->id;                
                $this->detailRepo->create($arg); //vamos guardando cada uno de los detalles de la orden
            }

            //Hacemos conexion con el drive y creamos el folder de la orden. Metodos en Helper.php
            $order_folder = Conection_Drive()->files->create(Create_Folder($args['name'], $this->documentRepo->getFolderOrders($args['project_id'])->drive_id), ['fields' => 'id']);
            $doc_ref_order = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_order->parent_document_id = $this->documentRepo->getFolderOrders($args['project_id'])->id;
            $doc_ref_order->name = $args['name'];
            $doc_ref_order->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_order->project_id = $args['project_id'];
            $doc_ref_order->module_id = 5; //id 5 pertenece al modulo Order
            $doc_ref_order->order_id = $order->id; 
            $doc_ref_order->drive_id = $order_folder->id;
            $doc_ref_order->save();     //guardamos el registro del folder raiz de la orden 

            $order_doc['order_id'] = $arg['order_id'];
            $order_doc['document_type'] = 0; // 0 = application_quote
            $order_doc['code'] = 'SC_'.$order->id.'_'.date("d").date("m").date("y"); 
            $order_doc['date'] = now();
            $order_document = $this->order_docRepo->create($order_doc); //guardamos el registro del order_doc para saber si es Solicitud, orden o factura
            
            $emails = $args['email_contacts']; //Array con ID de posibles proveedores
            foreach ($emails as $ema ) { 

                $user = DB::select('select * from contacts where id = ?', [1]);
                $data = [
                    'title' => 'prueba5',
                    'heading' => 'Hello from Ide@Soft',
                    'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,
                        when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                        It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.',
                    'user' => $user[0]
                ];     
                
                $pdf = PDF::loadView('solicitud', $data);   //Creacion del PDF  
                $pdf_name = $order_doc['code'].$this->contactRepo->find($ema)->name;          
                $pdf->save(storage_path('pdf').'/'.$pdf_name.'.pdf');            
                $adapter = new GoogleDriveAdapter(Conection_Drive(), $order_folder->id); //Cargar pdf en el drive
                $filesystem = new Filesystem($adapter);             
                $files = Storage::files();  // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
                foreach ($files as $file) { // recorremos cada uno de los file encontrados
                    $read = Storage::get($file);                    // leemos el contenido del PDF
                    $archivo = $filesystem->write($file, $read);    // Guarda el archivo en el drive
                    $file_id = $filesystem->getMetadata($file);     // get data de file en Drive
                    Storage::delete($pdf_name.'.pdf');   //eliminamos el file del Storage, ya que se encuentra cargado en el drive
                }

                $doc_ref_file = new Document_reference;
                $doc_ref_file->parent_document_id = $this->documentRepo->getFolderOrderCurrent($order->id)->id;
                $doc_ref_file->name = $pdf_name.'.pdf';
                $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_file->project_id = $args['project_id'];
                $doc_ref_file->module_id = 5; //id 5 pertenece al modulo order
                $doc_ref_file->order_document_id = $order_document->id; 
                $doc_ref_file->drive_id = $file_id['path'];
                $doc_ref_file->save();  //guardamos registro del del PDF generado y cargado en el drive
                 
                $quotation = new Quotation;
                $quotation->order_id = $order->id;
                $quotation->contact_id = $ema;                
                $quotation->authorized = false;                
                $quotation->save();     //guardamos la cotizacion solicitada            
                
                $hashed = Hash::make('quotation', [
                    'memory' => 1024,
                    'time' => 2,
                    'threads' => 2,
                ]);     //generamos hash
                $quotation_hash = Crypt::encryptString($quotation->id.'_'.$hashed); //encryptamos el id con el hash 
                
                $this->quotationRepo->updateQuotation($quotation->id, $quotation_hash); //Actualizamos el id de la cotizacion, poniendo el hash encriptado
                //Envio de correo a cada uno de los contactos
                Mail::to(User::find($ema)->email)->send(new RequestForQuotation(User::find($ema), Document_reference::find($doc_ref_file->id), Quotation::find($quotation->id)));
            }
            return $order;
        }, 3);
        return [
            'order' => $ord,
            'message' => 'Solicitud guardada correctamente'
        ];
    }
}
