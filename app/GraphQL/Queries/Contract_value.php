<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ProjectRepository;
use DB;

class Contract_value
{

    protected $projectRepo;

    public function __construct(ProjectRepository $proRepo)
    {
        $this->projectRepo = $proRepo;
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
        $total_added = 0;
        $added_amounts = $this->projectRepo->get_contract_value($args['project_id']);
        $project_amount = $this->projectRepo->find($args['project_id']);

        $total_amount['amount'] = $project_amount->contract_value;
        foreach ($added_amounts as $add)
        {
            $total_added += $add->amount;
        }
        $total_amount['amount'] += $total_added;
        return $total_amount;
    }
}
