<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;
use App\Repositories\ActivityRepository;


class DeleteActivity
{

    protected $activityRepo;

    public function __construct(ActivityRepository $actRepo)
    {
        $this->activityRepo = $actRepo;
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
        dd();
        $activity = $this->activityRepo->find($args['id']);
        if ($activity->parent_activity_id == null) {
                
        }
        DB::select('select * from users where active = ?', [1]);
    }
}
