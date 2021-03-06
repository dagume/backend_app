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
use App\Repositories\ProjectRepository;
use App\Repositories\ProductRepository;
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
    protected $projectRepo;
    protected $productRepo;

    public function __construct(ProductRepository $prodRepo, ProjectRepository $proRepo, OrderRepository $ordRepo, QuotationRepository $quoRepo, DetailRepository $detRepo, Document_referenceRepository $docRepo, Order_documentRepository $ordocRepo, ContactRepository $conRepo)
    {
        $this->quotationRepo = $quoRepo;
        $this->orderRepo = $ordRepo;
        $this->detailRepo = $detRepo;
        $this->documentRepo = $docRepo;
        $this->order_docRepo = $ordocRepo;
        $this->contactRepo = $conRepo;
        $this->projectRepo = $proRepo;
        $this->productRepo = $prodRepo;

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
        global $order_folder;
        try {
        $ord = DB::transaction(function () use($args){  //se crea la transacion
            $args['application_date']   = now();
            $args['state']              = 0; //el valor 0 es el estado de Application
            $args['sender_data']        = auth()->user()->id;
            $order = $this->orderRepo->create($args);   //creamos la nueva orden

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

            $order_doc['order_id'] = $order->id;
            $order_doc['document_type'] = 0; // 0 = application_quote
            $order_doc['code'] = 'SC_'.$order->id.'_'.date("d").date("m").date("y");
            $order_doc['date'] = now();
            $order_document = $this->order_docRepo->create($order_doc); //guardamos el registro del order_doc para saber si es Solicitud, orden o factura

            $emails = $args['email_contacts']; //Array con ID de posibles proveedores
            foreach ($emails as $ema ) {
                $quotation = new Quotation;
                $quotation->order_id = $order->id;
                $quotation->contact_id = $ema;
                $quotation->authorized = false;
                $quotation->received = false;
                $quotation->save();     //guardamos la cotizacion solicitada

                //Traemos servicio de trasnporte para agregarlo al detalle de la cotizacion
                $producto = $this->productRepo->getTransport();
                $detTransport['product_id'] = $producto->id;
                $detTransport['tax_id'] = $producto->tax_id;
                $detTransport['quo_id'] = $quotation->id;
                $detTransport['mea_id'] = 3;
                $detTransport['quantity'] = 1;
                $this->detailRepo->create($detTransport); //Registramos el servicio de transporte en la orden

                foreach ($args['updetails'] as $arg) {
                    $arg['quo_id'] = $quotation->id;
                    $arg['tax_id'] = $this->productRepo->find($arg['product_id'])->tax_id;
                    $details[] = $this->detailRepo->create($arg); //vamos guardando cada uno de los detalles de la orden
                }

                $data = [
                    'title' => 'Solicitud de Cotización',
                    'code' => $order_doc['code'],
                    'provider' => $this->contactRepo->find($ema),
                    'sender' => $this->contactRepo->find($order->sender_data),
                    'details' => $this->detailRepo->getDataPDF($quotation->id),
                    'project' => $this->projectRepo->find($args['project_id'])
                ];
                //dd($data);

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

                $hashed = Hash::make('quotation', [
                    'memory' => 1024,
                    'time' => 2,
                    'threads' => 2,
                ]);     //generamos hash
                $quotation_hash = Crypt::encryptString($quotation->id.'_'.$hashed); //encryptamos el id con el hash

                $this->quotationRepo->updateQuotation($quotation->id, $quotation_hash); //Actualizamos el id de la cotizacion, poniendo el hash encriptado
                //Envio de correo a cada uno de los contactos
                Mail::to(User::find($ema)->email)->send(new RequestForQuotation(Document_reference::find($doc_ref_file->id), Quotation::find($quotation->id), $order));
            }
            return $order;
        }, 3);
    } catch (Exception $e) {
        global $order_folder;
        //dd($project_folder->id);
        if (!is_null ($order_folder) || !empty($order_folder))
        {
            Conection_Drive()->files->delete($order_folder->id);
        }
        return [
            'order' => null,
            'message' => 'La solicitud de cotizacion no fue registrada, vuelva a intentarlo',
            'type' => 'Failed'
        ];
    }
        return [
            'order' => $ord,
            'message' => 'Solicitud enviada correctamente',
            'type' => 'Successful'
        ];
    }
}
