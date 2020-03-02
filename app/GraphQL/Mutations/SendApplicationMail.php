<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Document_reference;
use App\Quotation;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Order_documentRepository;
use App\Repositories\Document_referenceRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\ContactRepository;
use App\Repositories\OrderRepository;
use App\Repositories\DetailRepository;
use App\Mail\RequestForQuotation;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use DB;

class SendApplicationMail
{
    protected $order_docRepo;
    protected $documentRepo;
    protected $quotationRepo;
    protected $contactRepo;
    protected $orderRepo;
    protected $detailRepo;

    public function __construct(Order_documentRepository $ordocRepo, Document_referenceRepository $docRepo, QuotationRepository $quoRepo, ContactRepository $conRepo, OrderRepository $ordRepo, DetailRepository $detRepo)
    {
        $this->order_docRepo = $ordocRepo;
        $this->documentRepo = $docRepo;
        $this->quotationRepo = $quoRepo;
        $this->contactRepo = $conRepo;
        $this->orderRepo = $ordRepo;
        $this->detailRepo = $detRepo;
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
        $orde = DB::transaction(function () use($args){  //se crea la transacion
            $order = $this->orderRepo->find($args['order_id']);
            $ord['state'] = 0;  //el valor 0 es el estado de application
            $this->orderRepo->update($order->id, $ord);
            $emails = $args['email_contacts']; //Array con ID de posibles proveedores
            foreach ($emails as $ema ) {
                $quoOrd = $this->quotationRepo->QuotationsForOrder($args['order_id']);
                $quotation = new Quotation;
                $quotation->order_id = $args['order_id'];
                $quotation->contact_id = $ema;
                $quotation->authorized = false;
                $quotation->received = false;
                $quotation->save();     //guardamos la cotizacion solicitada

                $details = $this->detailRepo->getDetailQuo($quoOrd[0]->id);

                foreach ($details as $det) {
                    $detail['product_id'] = $det->product_id;
                    $detail['quo_id'] = $quotation->id;
                    $detail['mea_id'] = $det->mea_id;
                    $detail['quantity'] = $det->quantity;
                    $this->detailRepo->create($detail);
                    //dd($this->detailRepo->create($detail));
                }
                $data = [
                    'title' => 'Solicitud de Cotización',
                    'code' => $this->order_docRepo->getOrderDoc($args['order_id'], 0)->code, //consultamos el order doument, 0 = tipo de documento(Solicitud)
                    'provider' => $this->contactRepo->find($ema),
                    'sender' => $this->contactRepo->find($order->sender_data),
                    'details' => $this->detailRepo->getDataPDF( $quotation->id),
                    'project' => $this->documentRepo->getFolderOrderCurrent($args['order_id'])->project_id
                ];

                $pdf = PDF::loadView('solicitud', $data)->setPaper('a4');   //Creacion del PDF
                $pdf_name = $this->order_docRepo->getOrderDoc($args['order_id'], 0)->code.$this->contactRepo->find($ema)->name;
                $pdf->save(storage_path('pdf').'/'.$pdf_name.'.pdf');


                //$pdf->save(storage_path('pdf').'/solicitud.pdf');
                $adapter    = new GoogleDriveAdapter(Conection_Drive(), $this->order_docRepo->getFolderOrder($args['order_id'])->drive_id); //Cargar pdf en el drive
                $filesystem = new Filesystem($adapter);
                $files = Storage::files();      // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
                foreach ($files as $file) {     // recorremos cada uno de los file encontrados
                    $read = Storage::get($file);                    // leemos el contenido del PDF
                    $archivo = $filesystem->write($file, $read);    // Guarda el archivo en el drive
                    $file_id = $filesystem->getMetadata($file);     // get data de file en Drive
                    Storage::delete($pdf_name.'.pdf');   //eliminamos el file del Storage, ya que se encuentra cargado en el drive
                }

                $doc_ref_file = new Document_reference;
                $doc_ref_file->parent_document_id = $this->documentRepo->getFolderOrderCurrent($args['order_id'])->id;
                $doc_ref_file->name = $pdf_name.'.pdf';
                $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_file->project_id = $this->documentRepo->getFolderOrderCurrent($args['order_id'])->project_id;
                $doc_ref_file->module_id = 5; //id 5 pertenece al modulo order
                $doc_ref_file->order_document_id = $this->order_docRepo->getOrderDoc($args['order_id'], 0)->id;
                $doc_ref_file->drive_id = $file_id['path'];
                $doc_ref_file->save();  //guardamos registro del del PDF generado y cargado en el drive

                //$quotation = new Quotation;
                //$quotation->order_id = $args['order_id'];
                //$quotation->contact_id = $ema;
                //$quotation->authorized = false;
                //$quotation->save();     //guardamos la cotizacion solicitada

                $hashed = Hash::make('quotation', [
                    'memory' => 1024,
                    'time' => 2,
                    'threads' => 2,
                ]);     //generamos hash
                $quotation_hash = Crypt::encryptString($quotation->id.'_'.$hashed); //encryptamos el id con el hash

                $this->quotationRepo->updateQuotation($quotation->id, $quotation_hash); //Actualizamos el id de la cotizacion, poniendo el hash encriptado
                //Envio de correo a cada uno de los contactos
                Mail::to(User::find($ema)->email)
                ->send(new RequestForQuotation(Document_reference::find($doc_ref_file->id), Quotation::find($quotation->id), $order));
            }
            return $order;
        }, 3);
        return [
            'order' => $orde,
            'message' => 'Solicitud Enviada correctamente'
        ];
    }
}
