<?php

namespace App\GraphQL\Mutations;

use App\Quotation;
use App\Document_reference;
use App\Order;
use App\User;
use App\Mail\RequestForQuotation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\QuotationRepository;
use App\Repositories\DetailRepository;
use App\Repositories\OrderRepository;
use App\Repositories\Order_documentRepository;
use App\Repositories\ContactRepository;
use App\Repositories\Document_referenceRepository;
use App\Repositories\RoleRepository;
use DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;

class SendBuyOrder
{
    protected $quotationRepo;
    protected $detailRepo;
    protected $orderRepo;
    protected $order_docRepo;
    protected $contactRepo;
    protected $documentRepo;
    protected $roleRepo;

    public function __construct(RoleRepository $rolRepo, QuotationRepository $quoRepo, DetailRepository $detRepo, OrderRepository $ordRepo, Order_documentRepository $ordocRepo, ContactRepository $conRepo, Document_referenceRepository $docRepo)
    {
        $this->quotationRepo = $quoRepo;
        $this->detailRepo = $detRepo;
        $this->orderRepo = $ordRepo;
        $this->order_docRepo = $ordocRepo;
        $this->contactRepo = $conRepo;
        $this->documentRepo = $docRepo;
        $this->roleRepo = $rolRepo;

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
        $buy_order = DB::transaction(function () use($args){  //se crea la transacion
            $subtotal_with_iva = 0;
            $subtotal = 0;
            $quotation = $this->quotationRepo->find($args['quo_id']);

            if ($quotation->authorized == true) {

                $details = $this->detailRepo->getDetailQuo($args['quo_id']);
                foreach ($details as $det) {
                    $subtotal_with_iva += $det->subtotal;
                    $subtotal += $det->value * $det->quantity;
                }

                if ($quotation->discount_type == 1) {
                    $discount = round($subtotal_with_iva * ($quotation->discount / 100)); // porcentaje de descuento
                } else {
                    $discount = round($subtotal * ($quotation->discount / 100)); // porcentaje de descuento
                }

                $order['contact_id'] = $quotation->contact_id;
                $order['state'] = 2; // 2 = estado Orden abierta
                $order['subtotal'] = $subtotal_with_iva;
                $order['total'] = $subtotal_with_iva - $discount; //Total de la orden con descuento
                $order['pending_debt'] = $order['total'];
                $updated_order = $this->orderRepo->update($quotation->order_id, $order);

                $order_doc['order_id'] = $quotation->order_id;
                $order_doc['document_type'] = 1; // 1 = order_buy
                $order_doc['code'] = 'ORD_'.$quotation->order_id.'_'.date("d").date("m").date("y");
                $order_doc['date'] = now();
                $order_document = $this->order_docRepo->create($order_doc); //guardamos el registro del order_doc para saber si es Solicitud, orden o factura
                $contact = $this->contactRepo->find($order['contact_id']);
                $data = [
                    'title' => 'Orden de compra',
                    'code' => $order_doc['code'],
                    'provider' => $contact,
                    'sender' => $this->contactRepo->find($updated_order->sender_data),
                    'details' => $this->detailRepo->getDataPDF($quotation->id),
                    'quotation' => $quotation,
                    'discount' => $discount,
                    'project' => $this->quotationRepo->getAssociation($args['quo_id']) //verificamos si es consorcio o no el proyecto actual
                ];
                $pdf = PDF::loadView('orden', $data);   //Creacion del PDF
                $pdf_name = $order_doc['code'].$this->contactRepo->find($updated_order->contact_id)->name;
                $pdf->save(storage_path('pdf').'/'.$pdf_name.'.pdf');
                $adapter = new GoogleDriveAdapter(Conection_Drive(), $this->documentRepo->getFolderOrderCurrent($updated_order->id)->drive_id); //ubicamos carpeta donde vamos a guardar el drive
                $filesystem = new Filesystem($adapter);
                $files = Storage::files();  // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
                foreach ($files as $file) { // recorremos cada uno de los file encontrados
                    $read = Storage::get($file);                    // leemos el contenido del PDF
                    $archivo = $filesystem->write($file, $read);    // Guarda el archivo en el drive
                    $file_id = $filesystem->getMetadata($file);     // get data de file en Drive
                    Storage::delete($pdf_name.'.pdf');   //eliminamos el file del Storage, ya que se encuentra cargado en el drive
                }
                $doc_ref_file = new Document_reference;
                $doc_ref_file->parent_document_id = $this->documentRepo->getFolderOrderCurrent($updated_order->id)->id;
                $doc_ref_file->name = $pdf_name.'.pdf';
                $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_file->project_id = $updated_order->project_id;
                $doc_ref_file->module_id = 5; //id 5 pertenece al modulo order
                $doc_ref_file->order_document_id = $order_document->id;
                $doc_ref_file->drive_id = $file_id['path'];
                $doc_ref_file->save();  //guardamos registro del del PDF generado y cargado en el drive
                Mail::to(User::find($updated_order->contact_id)->email)
                    ->send(new RequestForQuotation(Document_reference::find($doc_ref_file->id), Quotation::find($quotation->id), Order::find($updated_order->id)));

                    //Asignamos a este contacto como proveedor del proyecto
                    /////////////////////$member['project_id'] = $updated_order->project_id;
                    /////////////////////$member['contact_id'] = $contact->id;
                    /////////////////////$member['role_id'] = $this->roleRepo->getRolProveedor()->id;;
                    /////////////////////$this->memberRepo->create($member);
                return $message = 'Orden de Compra enviada.';
            }else {
                return $message = 'No han autorizado esta compra';
            }
        }, 3);
        return ['message' => $buy_order];
    }
}
