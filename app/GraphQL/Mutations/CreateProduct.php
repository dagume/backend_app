<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ProductRepository;
use DB;

class CreateProduct
{
    protected $productRepo;

    public function __construct(ProductRepository $produRepo)
    {
        $this->productRepo = $produRepo;

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
        $prod = DB::transaction(function () use($args){
            //$args['name'] = trim(strtolower($args['name']));
            $product = $this->productRepo->create($args); //guarda registro del nuevo Producto
            return $product;
        }, 3);
        //\Nuwave\Lighthouse\Execution\Utils\Subscription::broadcast('productCreate', $product);
        return [
            'product' => $prod,
            'message' => 'Producto creado exitosamente',
            'type' => 'Successful'
        ];
    }
}
