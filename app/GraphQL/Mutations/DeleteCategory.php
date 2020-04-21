<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Category;

class DeleteCategory
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
			$category = Category::find($args['id']);
			$category->delete();
		}
        catch (\Illuminate\Database\QueryException $e)
        {
			return [
                'category' => null,
                'message' => 'Esta categoria no se puede eliminar, ya tiene productos asignados',
                'type' => 'Failed'
            ];
        }
        return [
            'category' => $category,
            'message' => 'Categoria eliminada exitosamente',
            'type' => 'Successful'
        ];
    }
}
