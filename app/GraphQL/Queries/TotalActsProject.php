<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ActivityRepository;
use DB;

class TotalActsProject
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
        $total = 0;
        $acts = $this->activityRepo->totalActsProject($args['project_id']);
        foreach ($acts as $act) {
            $total += $act->amount;
        }

        return[
            'totalActs' => $total
        ];
    }
}
