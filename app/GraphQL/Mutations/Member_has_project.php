<?php

namespace App\GraphQL\Mutations;

use App\Member;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\MemberRepository;
use App\Repositories\ContactRepository;
use App\Repositories\RoleRepository;
use DB;

class Member_has_project
{
    protected $memberRepo;
    protected $contactRepo;
    protected $roleRepo;

    public function __construct(RoleRepository $rolRepo, ContactRepository $conRepo, MemberRepository $memRepo)
    {
        $this->memberRepo = $memRepo;
        $this->contactRepo = $conRepo;
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
        $mem = DB::transaction(function () use($args){
            $role_id = $this->roleRepo->getRolProject()->id;
            $contact = $this->contactRepo->find($args['contact_id']);
            if($contact->type != 0){
                $args['state'] = 1;
                $member = $this->memberRepo->create($args);
                $message = 'Integrante agregado exitosamente';
            }elseif ($args['role_id'] == $role_id) {
                $args['state'] = 1;
                $member = $this->memberRepo->create($args);

                $mem['project_id'] = strstr($contact->name, '_', true); //sacamos el project_id del nombre del contacto
                $mem['contact_id'] = $this->memberRepo->getMemberProject($args['project_id'])->contact_id;
                $mem['role_id'] = $this->roleRepo->getRolProject()->id;
                $mem['state'] = 1;
                $memberDos = $this->memberRepo->create($mem);
                $message = 'Integrante agregado exitosamente';
            }else{
                $message = 'El contacto no puede ser agregado como Integrante';
            }

            return [
                'member' => $member,
                'message' => $message
            ];
        }, 3);
        return $mem;

    }
}
