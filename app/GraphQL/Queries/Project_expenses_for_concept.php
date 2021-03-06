<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Accounting_movementRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ContactRepository;
use DB;

class Project_expenses_for_concept
{
    protected $accountingRepo;
    protected $roleRepo;
    protected $contactRepo;

    public function __construct(ContactRepository $conRepo, RoleRepository $rolRepo, Accounting_movementRepository $acoRepo)
    {
        $this->accountingRepo = $acoRepo;
        $this->roleRepo = $rolRepo;
        $this->contactRepo = $conRepo;
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
        $role_project = $this->roleRepo->getRolProject();
        $contact_project = $this->contactRepo->getContactIdentificatioNumber($args['project_id']);
        $movements = $this->accountingRepo->project_expenses_for_concept($args['project_id'], $contact_project->id, $role_project->id, $args['puc_id']);
        return $movements;
    }
}
