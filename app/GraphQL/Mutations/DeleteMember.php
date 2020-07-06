<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\MemberRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ContactRepository;
use App\Repositories\Accounting_movementRepository;
use DB;

class DeleteMember
{
    protected $memberRepo;
    protected $roleRepo;
    protected $contactRepo;
    protected $accountRepo;

    public function __construct(Accounting_movementRepository $accoRepo, ContactRepository $conRepo, RoleRepository $rolRepo, MemberRepository $memRepo)
    {
        $this->memberRepo = $memRepo;
        $this->roleRepo = $rolRepo;
        $this->contactRepo = $conRepo;
        $this->accountRepo = $accoRepo;
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
        $member = $this->memberRepo->find($args['member_id']);
        $contact = $this->contactRepo->find($member->contact_id);
        $project_role = $this->roleRepo->getRolProject()->id;

        if ($contact->identification_number == $member->project_id){
            return [
                'member' => null,
                'message' => 'No se puede eliminar, Este hace referencia a la cuenta principal del proyecto',
                'type' => 'Failed'
            ];
        }
        if (!$this->accountRepo->movements_for_member($args['member_id'])) {
            return [
                'member' => null,
                'message' => 'El integrante ya tiene movimientos registrados, no se puede eliminar',
                'type' => 'Failed'
            ];
        }

        try
		{
            $delete_mem = DB::transaction(function () use($args){//se crea la transacion

                $this->accountRepo->movements_for_member($args['member_id']);

                //Si es role proyecto se deben eliminar dos registros de member
                if ($member->role_id == $project_role) {
                    $cont = $this->contactRepo->getContactIdentificatioNumber($member->project_id);
                    $mem = $this->memberRepo->get_member($contact->identification_number, $cont->id, $project_role)[0];
                    $member2 = $this->memberRepo->find($mem->id);
                    $this->memberRepo->delete($member);
                    $this->memberRepo->delete($member2);
                }else {
                    $this->memberRepo->delete($member);
                }
                return $member;
            }, 3);
        }
        catch (\Illuminate\Database\QueryException $e) //exception
        {
            return [
                'member' => null,
                'message' => 'Error, intente eliminar de nuevo',
                'type' => 'Failed'
            ];
        }
            return[
                'member'=> $delete_mem,
                'message'=> 'Integrante eliminado Exitosamente',
                'type' => 'Successful'
            ];
    }
}
