<?php

namespace App\GraphQL\Mutations;

use App\Member;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\MemberRepository;
use App\Repositories\ContactRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ProjectRepository;
use DB;

class Member_has_project
{
    protected $memberRepo;
    protected $contactRepo;
    protected $roleRepo;
    protected $projectRepo;

    public function __construct(ProjectRepository $proRepo, RoleRepository $rolRepo, ContactRepository $conRepo, MemberRepository $memRepo)
    {
        $this->memberRepo = $memRepo;
        $this->contactRepo = $conRepo;
        $this->roleRepo = $rolRepo;
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
        if ($this->projectRepo->find($args['project_id'])->state !== '5') {
            $mem = DB::transaction(function () use($args){
                $member = null;
                $role_id = $this->roleRepo->getRolLender()->id; //El proyecto puede ser miembro de otro con el rol de prestamista, se hace la validacion mas adelante
                $contact = $this->contactRepo->find($args['contact_id']);
                if($contact->type != 0){
                    $args['state'] = 1;
                    $member = $this->memberRepo->create($args);
                    $message = 'Integrante agregado exitosamente';
                }elseif ($args['role_id'] == $role_id) {
                    $args['state'] = 1;
                    $member = $this->memberRepo->create($args);

                    $mem['project_id'] = $contact->identification_number;
                    $mem['contact_id'] = $this->memberRepo->getMemberProject($args['project_id'])->contact_id;
                    $mem['role_id'] = $this->roleRepo->getRolProject()->id;
                    $mem['state'] = 1;
                    $memberDos = $this->memberRepo->create($mem);
                    $message = 'Integrante agregado exitosamente';
                }else{
                    $message = 'El contacto no puede ser agregado como Integrante, si es otro proyecto asegurece de ser agregado como prestamista';
                }
                return [
                    'member' => $member,
                    'message' => $message,
                    'type' => 'Successful'
                ];
            }, 3);
            return $mem;
        }else {
            return [
                'member' => null,
                'message' => 'Este proyecto esta archivado, no es posible agregar un nuevo integrante.',
                'type' => 'Failed'
            ];
        }

    }
}
