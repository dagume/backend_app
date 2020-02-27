<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\QuotationRepository;
use App\Repositories\OrderRepository;
use App\Quotation;

use DB;
class UpdateQuotation
{
    protected $quotationRepo;
    protected $orderRepo;

    public function __construct(QuotationRepository $quoRepo, OrderRepository $ordRepo)
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
        $quo = DB::transaction(function () use($args){  //se crea la transacion
            $quotation = $this->quotationRepo->find($args['id']); //consultamos la cotizacion a autorizar
            $quotations = $this->quotationRepo->QuotationsForOrder($quotation->order_id);
            foreach ($quotations as $quos) {
                $authorized['authorized'] = false;
                $this->quotationRepo->update($quos->id, $authorized);
            }
            $quo['authorized'] = true; //cambiamos el estadp de la quotation (Esta sera la cotizacion autorizada)
            $quo['date'] = now(); //registramos la fecha de autorizacion
            $order = $this->orderRepo->find($quotation->order_id);// Consultamos la orden para luego cambiar su sestado a Approved
            $ord['state'] = 1; //cambiamos el estado de la orden
            $update_quo = $this->quotationRepo->update($args['id'], $quo); //actualizamos cotizacion a aprovada
            $this->orderRepo->update($order->id, $ord); //actializamos estado de la orden a Approved
            return $update_quo;
        }, 3);
        return [
            'quotation' => $quo,
            'message'=> 'Cotización autorizada'
        ];
    }
}
