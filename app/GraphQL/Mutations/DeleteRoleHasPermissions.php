<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;
use DB;

class DeleteRoleHasPermissions
{
    protected $roleRepository;
    protected $permissionRepository;

    public function __construct(PermissionRepository $perRepo, RoleRepository $rolRepo)
    {
        $this->roleRepository = $rolRepo;
        $this->permissionRepository = $perRepo;
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
        $rol = DB::transaction(function () use($args){
            $role = $this->roleRepository->find($args['role_id']);
            foreach ($args['permissions_id'] as $arg) {
                $role->revokePermissionTo($this->permissionRepository->find($arg));
            }
            return $role;
        }, 3);

        return[
            'role' => $rol,
            'message' => 'Permisos del role eliminados'
        ];
    }
}
