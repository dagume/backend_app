<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\PaymentAgreementRepository;
use DB;

class ProjectPayAgreMonth
{
    protected $payment;

    public function __construct(PaymentAgreementRepository $payRepo)
    {
        $this->payment = $payRepo;
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
        $first_date = '\''.$args['first_date'].'\'';
        $end_date = '\''.$args['end_date'].'\'';
        $payments = $this->payment->getprojectPayAgreMonth($args['project_id'], $first_date, $end_date);
        return $payments;
    }
}
