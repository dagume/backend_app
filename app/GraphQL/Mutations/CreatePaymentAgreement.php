<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\PaymentAgreementRepository;
use App\Repositories\OrderRepository;
use DB;

class CreatePaymentAgreement
{
    protected $paymentRepo;
    protected $orderRepo;

    public function __construct(PaymentAgreementRepository $payRepo, OrderRepository $ordRepo)
    {
        $this->paymentRepo = $payRepo;
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
        $data = DB::transaction(function () use($args){  //se crea la transacion
            if ($args['state'] == false) {
                //$this->paymentRepo->create($args);
            }else {
                $order = $this->orderRepo->find($args['order_id']); 
                $pending['pending_debt'] = $order->pending_debt - $args['pending_debt'];        
                //$this->orderRepo->update($order->id, $pending);
            }
        }, 3);
    }
}
