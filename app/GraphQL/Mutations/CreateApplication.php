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
            $args['application_date']   = now();
            $args['state']              = 0; //el valor 0 es el estado de Application
            $args['sender_data']        = auth()->user()->id;
            $order = $this->orderRepo->create($args);

            foreach ($args['updetails'] as $arg) {
                $arg['order_id'] = $this->orderRepo->lastOrder()->id;
                $this->detailRepo->create($arg);
            }
            //$data = ['title' => 'Solicitud de cotizacion'];
            //$pdf = PDF::loadView('solicitud', $data);
            //$pdf->download('itsolutionstuff.pdf');
            $emails = $args['email_contacts'];
            foreach ($emails as $ema ) {
                Mail::to(User::find($ema)->email)->send(new RequestForQuotation(User::find($ema)));
                $quotation['order_id'] = $this->orderRepo->lastOrder()->id;
                $quotation['contact_id'] = $ema;
                $this->quotationRepo->create($quotation);
            }
            return $order;
        }, 3);
        return [
            'order' => $ord,
            'message' => 'Solicitud Enviada correctamente'
        ];
    }
}
