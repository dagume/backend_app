<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\DetailRepository;
use App\Repositories\QuotationRepository;
use DB;

class UpdateDetailQuotation
{
    protected $detailRepo;
    protected $quotationRepo;

    public function __construct(DetailRepository $detRepo, QuotationRepository $quoRepo)
    {
        $this->detailRepo = $detRepo;
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
            foreach ($args['detailsOrder'] as $arg) {    //Vamos actualizando los detalles de la orden actual
                $arg['subtotal'] = $arg['quantity'] * $arg['value'];
                $detail = $this->detailRepo->update($arg['id'], $arg); //vamos actualizando cada uno de los detalles de la orden
            }
            $this->quotationRepo->update($detail->quo_id, $args);
        }, 3);
        return [
            'message' => 'Cotazación registrada'
        ];
    }
}
