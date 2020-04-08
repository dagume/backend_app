<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\MeasureRepository;
use DB;
class CreateMeasure
{
    protected $measureRepo;

    public function __construct(MeasureRepository $meaRepo)
    {
        $this->measureRepo = $meaRepo;
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
        $mea = DB::transaction(function () use($args){

            try
            {
                $measure = $this->measureRepo->create($args);
                return $measure;
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                return [
                    'measure' => null,
                    'message' => 'Error, vuelva a intentar',
                    'type' => 'Failed'
                ];
            }

        }, 3);
        return [
            'measure' => $mea,
            'message' => 'Unidad creada con exito',
            'type' => 'Successful'
        ];
    }
}
