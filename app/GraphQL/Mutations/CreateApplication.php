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
use DB;

class CreateApplication
{
    protected $orderRepo;
    protected $quotationRepo;    

    public function __construct(OrderRepository $ordRepo, QuotationRepository $quoRepo)
    {
        $this->quotationRepo = $quoRepo;
        $this->orderRepo = $ordRepo;
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
        DB::transaction(function () use($args){
            $args['application_date']   = now();
            $args['state']              = 0; //el valor 0 es el estado de Application           
            $args['sender_data']        = auth()->user()->id;
            $this->orderRepo->create($args);

            $emails = $args['email_contacts'];
            foreach ($emails as $ema ) {
                Mail::to(User::find($ema)->email)->send(new RequestForQuotation(User::find($ema)));                
                $quotation['order_id'] = $this->orderRepo->lastOrder()->id;
                $quotation['contact_id'] = $ema;                
                $this->quotationRepo->create($quotation);
            }
        }, 3);
        return [
            'message' => 'Solicitud Enviada correctamente'
        ];
    }
}
