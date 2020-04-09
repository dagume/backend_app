<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\PucRepository;
use DB;

class CreatePuc
{
    protected $pucRepo;

    public function __construct(PucRepository $puRepo)
    {
        $this->pucRepo = $puRepo;
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
            if (empty($args['description'])) {
                $args['description'] = null;
            }
            $puc = DB::insert('insert into puc (id, parent_puc_id, name, description) values (?, ?, ?, ?)', [$args['id'], $args['parent_puc_id'], $args['name'], $args['description']]);

        }catch (\Illuminate\Database\QueryException $e)
        {
            return [
                'measure' => null,
                //'message' => 'Error, vuelva a intentar',
                'message' => $e->getMessage(),
                'type' => 'Failed'
            ];
        }
        return [
            'puc' => $this->pucRepo->find($args['id']),
            'message' => 'Cuenta creada con exito',
            'type' => 'Successful'
        ];
    }
}
