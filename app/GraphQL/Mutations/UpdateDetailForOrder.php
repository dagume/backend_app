<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\DetailRepository;
use App\Repositories\OrderRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\Order_documentRepository;
use App\Repositories\ContactRepository;
use DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;

class UpdateDetailForOrder
{
    protected $detailRepo;
    protected $orderRepo;
    protected $quotationRepo;
    protected $order_docRepo;
    protected $contactRepo;


    public function __construct(DetailRepository $detRepo, OrderRepository $ordRepo, QuotationRepository $quoRepo, Order_documentRepository $ordocRepo, ContactRepository $conRepo)
    {
        $this->detailRepo = $detRepo;
        $this->orderRepo = $ordRepo;
        $this->quotationRepo = $quoRepo;
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
        $det = DB::transaction(function () use($args){  //se crea la transacion
            $order_subtotal = 0;            
            foreach ($args['detailsOrder'] as $arg) {    //Vamos actualizando los detalles de la orden actual           
                $arg['subtotal'] = $arg['quantity'] * $arg['value'];                   
                $updated_details = $this->detailRepo->update($arg['id'], $arg); //vamos actualizando cada uno de los detalles de la orden
                $order_subtotal += $arg['subtotal'];
            }
            //Buscamos las cotizaciones de la orden actual
            $quotations = $this->quotationRepo->QuotationsForOrder($updated_details->order_id);
            foreach ($quotations as $quo) {
                if ($quo->authorized == true) {
                    $iva = round($order_subtotal * 0.19, 2);
                    $order['contact_id'] = $quo->contact_id;
                    $order['state'] = 2; // 2 = estado Orden abierta
                    $order['subtotal'] = $order_subtotal;
                    $order['total'] = $order_subtotal + $iva;
                    $updated_order = $this->orderRepo->update($updated_details->order_id, $order);                    

/////////////////////////////aqui vamos tenemos que generar el PDF y guardar su registro
                    $order_doc['order_id'] = $updated_details->order_id;
                    $order_doc['document_type'] = 1; // 1 = order_buy
                    $order_doc['code'] = 'ORD_'.$updated_details->order_id.'_'.date("d").date("m").date("y"); 
                    $order_doc['date'] = now();
                    $order_document = $this->order_docRepo->create($order_doc); //guardamos el registro del order_doc para saber si es Solicitud, orden o factura

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

                    $pdf = PDF::loadView('orden', $data);   //Creacion del PDF  
                    $pdf_name = $order_doc['code'].$this->contactRepo->find($updated_order->contact_id)->name;          
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
                 
                }
            
            }            
        }, 3);
        return [            
            'message' => 'Orden de Compra enviada.'
        ];
    }   
}
