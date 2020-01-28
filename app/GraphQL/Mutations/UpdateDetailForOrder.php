<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\DetailRepository;
use App\Repositories\OrderRepository;
use App\Repositories\QuotationRepository;
use DB;

class UpdateDetailForOrder
{
    protected $detailRepo;
    protected $orderRepo;
    protected $quotationRepo;

    public function __construct(DetailRepository $detRepo, OrderRepository $ordRepo, QuotationRepository $quoRepo)
    {
        $this->detailRepo = $detRepo;
        $this->orderRepo = $ordRepo;
        $this->quotationRepo = $quoRepo;
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
        $det = DB::transaction(function () use($args){  //se crea la transacion
            $order_subtotal = 0;            
            foreach ($args['detailsOrder'] as $arg) {               
                $arg['subtotal'] = $arg['quantity'] * $arg['value'];                   
                $updated_details = $this->detailRepo->update($arg['id'], $arg); //vamos actualizando cada uno de los detalles de la orden
                $order_subtotal += $arg['subtotal'];
            }
            $iva = round($order_subtotal * 0.19, 2);
            $order['subtotal'] = $order_subtotal;
            $order['total'] = $order_subtotal + $iva;
            $updated_order = $this->orderRepo->update($updated_details->order_id, $order);                    

            //$this->quotationRepo->find
        }, 3);
        return [            
            'message' => 'Orden de Compra enviada.'
        ];
    }   
}
