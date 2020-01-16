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

    public function __construct(OrderRepository $ordRepo, QuotationRepository $quoRepo, DetailRepository $detRepo)
    {
        $this->quotationRepo = $quoRepo;
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
        //exportPdf();
        //dd($this->orderRepo->lastOrder()->id);
        $ord = DB::transaction(function () use($args){
            //$args['application_date']   = now();
            //$args['state']              = 0; //el valor 0 es el estado de Application
            //$args['sender_data']        = auth()->user()->id;
            //$order = $this->orderRepo->create($args);
//
            //foreach ($args['updetails'] as $arg) {
            //    $arg['order_id'] = $this->orderRepo->lastOrder()->id;
            //    $this->detailRepo->create($arg);
            //}
            $user = DB::select('select * from contacts where id = ?', [1]);
            
            $data = [
                'title' => 'prueba1',
                'heading' => 'Hello from 99Points.info',
                'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,
                    when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                    It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.',
                'user' => $user[0]];
                
                
            $pdf = PDF::loadView('solicitud', $data);
            $pdf->download('itsolutionstuff.pdf');
            $emails = $args['email_contacts'];
            //$pdf->save(storage_path('pdf').'/solicitud.pdf');
            dd($pdf->save(storage_path('pdf').'/solicitud.pdf'));

            //foreach ($emails as $ema ) {             
            //    $quotation['order_id'] = $this->orderRepo->lastOrder()->id;
            //    $quotation['contact_id'] = $ema;
            //    $quo = $this->quotationRepo->create($quotation);
            //    /////////////////////////////
            //    $hashed = Hash::make('quotation', [
            //        'memory' => 1024,
            //        'time' => 2,
            //        'threads' => 2,
            //    ]);
            //    $quotation_id = Crypt::encryptString($quo->id.'_'.$hashed); //encryptamos el id con el hash 
            //    
            //    $affected_id = DB::table('quotations') //Actualizamos el id de la cotizacion, poniendo el hash encriptado
            //    ->where('id', $quo->id)
            //    ->update(['hash_id' => $quotation_id]);
//
            //    Mail::to(User::find($ema)->email)->send(new RequestForQuotation(User::find($ema), Quotation::find($quo->id)));
            //}
            return $order;
        }, 3);
        return [
            'order' => $ord,
            'message' => 'Solicitud Enviada correctamente'
        ];
    }
}
