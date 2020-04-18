<?php

namespace App\GraphQL\Mutations;

use App\Measure;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteMeasure
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
			$measure = Measure::find($args['id']);
			$measure->delete();
		}
        catch (\Illuminate\Database\QueryException $e)
        {
			return [
                'product' => null,
                'message' => 'Esta unidad de medida no se puede eliminar, se está usando en ordenes de compra',
                'type' => 'Failed'
            ];
        }
        return [
            'product' => $measure,
            'message' => 'Unidad de medida eliminada exitosamente',
            'type' => 'Successful'
        ];
    }
}
