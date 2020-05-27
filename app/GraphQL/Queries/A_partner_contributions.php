<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Accounting_movementRepository;
use App\Repositories\RoleRepository;
use DB;

class A_partner_contributions
{
    protected $accountingRepo;
    protected $roleRepo;

    public function __construct(RoleRepository $rolRepo, Accounting_movementRepository $acoRepo)
    {
        $this->accountingRepo = $acoRepo;
        $this->roleRepo = $rolRepo;
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
        $role_socio = $this->roleRepo->getRolSocio()->id;
        $movements = $this->accountingRepo->partner_contributions ($args['project_id'], $args['contact_id'], $role_socio);
        return $movements;
    }
}
