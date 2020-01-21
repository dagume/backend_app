<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Order;
use App\Mail\RequestForQuotation;
use App\Quotation;
use App\Document_reference;
use Illuminate\Support\Facades\Mail;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\OrderRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\DetailRepository;
use App\Repositories\Document_referenceRepository;
use App\Repositories\Order_documentRepository;
use DB;
use Barryvdh\DomPDF\Facade as PDF;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;

class CreateApplication
{
    protected $orderRepo;
    protected $quotationRepo;
    protected $detailRepo;
    protected $documentRepo;
    protected $order_docRepo;

    public function __construct(OrderRepository $ordRepo, QuotationRepository $quoRepo, DetailRepository $detRepo, Document_referenceRepository $docRepo, Order_documentRepository $ordocRepo)
    {
        $this->quotationRepo = $quoRepo;
        $this->orderRepo = $ordRepo;
        $this->detailRepo = $detRepo;
        $this->documentRepo = $docRepo;
        $this->order_docRepo = $ordocRepo;
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
        //exportPdf();
        //dd($this->orderRepo->lastOrder()->id);
        $ord = DB::transaction(function () use($args){
            $args['application_date']   = now();
            $args['state']              = 0; //el valor 0 es el estado de Application
            $args['sender_data']        = auth()->user()->id;
            $order = $this->orderRepo->create($args);

            foreach ($args['updetails'] as $arg) {
                $arg['order_id'] = $order->id;                
                $this->detailRepo->create($arg);
            }

            //Hacemos conexion con el drive y creamos el folder de Contabilidad. Metodos en Helper.php
            $order_folder = Conection_Drive()->files->create(Create_Folder($args['name'], $this->documentRepo->getFolderOrders($args['project_id'])->drive_id), ['fields' => 'id']);
            $doc_ref_order = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_order->parent_document_id = $this->documentRepo->getFolderOrders($args['project_id'])->id;
            $doc_ref_order->name = $args['name'];
            $doc_ref_order->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_order->project_id = $args['project_id'];
            $doc_ref_order->module_id = 5; //id 5 pertenece al modulo Order
            $doc_ref_order->drive_id = $order_folder->id;
            $doc_ref_order->save();


            $order_doc['order_id'] = $arg['order_id'];
            $order_doc['document_type'] = 0; // 0 = application_quote
            $order_doc['code'] = 'SC_'.$order->id.'_'.date("d").date("m").date("y");
            $order_doc['date'] = now();
            $order_document = $this->order_docRepo->create($order_doc);
            /////////////

            $user = DB::select('select * from contacts where id = ?', [4]);
            $data = [
                'title' => 'prueba1',
                'heading' => 'Hello from Ide@Soft',
                'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,
                    when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                    It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.',
                'user' => $user[0]
            ];                
            
            //Creacion del PDF
            $pdf = PDF::loadView('solicitud', $data);
            //$pdf->download('itsolutionstuff.pdf');
            //$pdf->save(storage_path('pdf').'/'.$order_doc['code'].'.pdf');            
            $pdf->save(storage_path('pdf').'/solicitud.pdf');            
            //Cargar pdf en el drive
            $adapter    = new GoogleDriveAdapter(Conection_Drive(), $order_folder->id);
            $filesystem = new Filesystem($adapter);
            // here we are uploading files from local storage
            // we first get all the files
            $files = Storage::files();
            // loop over the found files
            foreach ($files as $file) {
                // read the file content
                $read = Storage::get($file);               
                // save to google drive
                $archivo = $filesystem->write($file, $read);
                // get data File in drive
                $prueba = $filesystem->getMetadata($file);
                Storage::delete('solicitud.pdf');
                //dd($prueba['path']);
            }
            $doc_ref_file = new Document_reference;
            $doc_ref_file->parent_document_id = $this->documentRepo->getFolderOrderCurrent($args['project_id'],$args['name'])->id;
            $doc_ref_file->name = $order_doc['code'];
            $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_file->project_id = $args['project_id'];
            $doc_ref_file->module_id = 5; //id 5 pertenece al modulo order
            $doc_ref_file->order_document_id = $order_document->id; 
            $doc_ref_file->drive_id = $prueba['path'];
            $doc_ref_file->save();

            $emails = $args['email_contacts'];
            foreach ($emails as $ema ) {             
                $quotation = new Quotation;
                $quotation->order_id = $order->id;
                $quotation->contact_id = $ema;                
                $quotation->save();                
                //$quo = $this->quotationRepo->create($quotation);
                /////////////////////////////
                $hashed = Hash::make('quotation', [
                    'memory' => 1024,
                    'time' => 2,
                    'threads' => 2,
                ]);
                $quotation_hash = Crypt::encryptString($quotation->id.'_'.$hashed); //encryptamos el id con el hash 
                
                $this->quotationRepo->updateQuotation($quotation->id, $quotation_hash); //Actualizamos el id de la cotizacion, poniendo el hash encriptado
                
                Mail::to(User::find($ema)->email)->send(new RequestForQuotation(User::find($ema), Document_reference::find($doc_ref_file->id)));
            }
            return $order;
        }, 3);
        return [
            'order' => $ord,
            'message' => 'Solicitud Enviada correctamente'
        ];
    }
}
