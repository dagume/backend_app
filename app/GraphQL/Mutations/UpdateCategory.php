<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\CategoryRepository;
use DB;

class UpdateCategory
{
    protected $categoryRepo;

    public function __construct(CategoryRepository $catRepo)
    {
        $this->categoryRepo = $catRepo;
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
        try
		{
            $category = $this->categoryRepo->update($args['id'], $args);
		}
        catch (\Exception $e)
        {
			return [
                'category' => null,
                'message' => 'Error, no se pudo editar. Vuelva a intentar',
                'type' => 'Failed'
            ];
        }
        return [
            'category' => $category,
            'message' => 'Categoria actualizada Exitosamente',
            'type' => 'Successful'
        ];
    }
}