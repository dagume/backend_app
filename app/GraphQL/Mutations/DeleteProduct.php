<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Product;

class DeleteProduct
{
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
        try
		{
			$product = Product::find($args['id']);
			$product->delete();
		}
        catch (\Illuminate\Database\QueryException $e)
        {
			return [
                'product' => null,
                'message' => 'Este producto no se puede eliminar, se está usando en ordenes de compra',
                'type' => 'Failed'
            ];
        }
        return [
            'product' => $product,
            'message' => 'Producto eliminado exitosamente',
            'type' => 'Successful'
        ];
    }
}
