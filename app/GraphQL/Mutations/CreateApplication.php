<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Order;
use App\Mail\RequestForQuotation;
use App\Quotation;
use Illuminate\Support\Facades\Mail;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\OrderRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\DetailRepository;
use App\Repositories\Document_referenceRepository;
use DB;
use Barryvdh\DomPDF\Facade as PDF;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;

class CreateApplication
{
    protected $orderRepo;
    protected $quotationRepo;
    protected $detailRepo;
    protected $documentRepo;

    public function __construct(OrderRepository $ordRepo, QuotationRepository $quoRepo, DetailRepository $detRepo, Document_referenceRepository $docRepo)
    {
        $this->quotationRepo = $quoRepo;
        $this->orderRepo = $ordRepo;
        $this->detailRepo = $detRepo;
        $this->documentRepo = $docRepo;
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

            //Hacemos conexion con el drive y creamos el folder de Contabilidad. Metodos en Helper.php
            $order_folder = Conection_Drive()->files->create(Create_Folder($args['name'], $this->documentRepo->getFolderOrders($args['project_id'])), ['fields' => 'id']);
            $doc_ref_order = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_order->parent_document_id = DB::table('document_reference')->where('name', $args['name'])->first()->id;
            $doc_ref_order->name = $args['name'];
            $doc_ref_order->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_order->project_id = $args['project_id'];
            $doc_ref_order->module_id = 2; //id 2 pertenece al modulo Project
            $doc_ref_order->drive_id = $order_folder->id;
            $doc_ref_order->save();

            foreach ($args['updetails'] as $arg) {
                $arg['order_id'] = $order->id;
                $this->detailRepo->create($arg);
            }
            $user = DB::select('select * from contacts where id = ?', [1]);
            $data = [
                'title' => 'prueba1',
                'heading' => 'Hello from Ide@Soft',
                'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,
                    when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                    It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.',
                'user' => $user[0]
            ];                
                
            $pdf = PDF::loadView('solicitud', $data);
            //$pdf->download('itsolutionstuff.pdf');
            $pdf->save(storage_path('pdf').'/solicitud.pdf');
            //dd($pdf->save(storage_path('pdf').'/solicitud.pdf'));
            $emails = $args['email_contacts'];
            foreach ($emails as $ema ) {             
                $quotation['order_id'] = $order->id;
                $quotation['contact_id'] = $ema;
                $quo = $this->quotationRepo->create($quotation);
                /////////////////////////////
                $hashed = Hash::make('quotation', [
                    'memory' => 1024,
                    'time' => 2,
                    'threads' => 2,
                ]);
                $quotation_id = Crypt::encryptString($quo->id.'_'.$hashed); //encryptamos el id con el hash 
                
                $affected_id = DB::table('quotations') //Actualizamos el id de la cotizacion, poniendo el hash encriptado
                ->where('id', $quo->id)
                ->update(['hash_id' => $quotation_id]);

                Mail::to(User::find($ema)->email)->send(new RequestForQuotation(User::find($ema), Quotation::find($quo->id)));
            }
            return $order;
        }, 3);
        return [
            'order' => $ord,
            'message' => 'Solicitud Enviada correctamente'
        ];
    }
}
