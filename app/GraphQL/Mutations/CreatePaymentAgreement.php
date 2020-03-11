<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\PaymentAgreementRepository;
use App\Repositories\OrderRepository;
use App\Repositories\Order_documentRepository;
use DB;

class CreatePaymentAgreement
{
    protected $paymentRepo;
    protected $orderRepo;
    protected $ord_documentRepo;

    public function __construct(Order_documentRepository $ord_docRepo, PaymentAgreementRepository $payRepo, OrderRepository $ordRepo)
    {
        $this->paymentRepo = $payRepo;
        $this->orderRepo = $ordRepo;
        $this->ord_documentRepo = $ord_docRepo;
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
        //$data = DB::transaction(function () use($args){  //se crea la transacion
        //    if ($args['state'] != false) {
        //        $order = $this->orderRepo->find($args['order_id']);
        //        $pending['pending_debt'] = $order->pending_debt - $args['pending_debt'];
        //        //$this->orderRepo->update($order->id, $pending);
//
        //        $movement['puc_id']= 5105; //registrar cuenta
        //        $movement['project_id']= $order->project_id;
        //        //$movement['destination_id']= ;
        //        //$movement['origin_id']= ;
        //        $movement['movement_date']= now();
        //        //$movement['payment_method']= ;
        //        $movement['value']= $args['amount'];
        //        $movement['code']= $this->ord_documentRepo->getCodeOrderBuy($args['order_id'])->code;
        //        $movement['registration_date']= now();
        //        $movement['sender_id']= auth()->user()->id;
        //        $movement['state_movement']= True;
        //    }
        //    $this->paymentRepo->create($args);
        //}, 3);
    }
}
