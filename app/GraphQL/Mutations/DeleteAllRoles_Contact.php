<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\MemberRepository;
use DB;

class DeleteAllRoles_Contact
{
    protected $memberRepo;

    public function __construct(MemberRepository $memRepo)
    {
        $this->memberRepo = $memRepo;
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
        try
		{
            $mem = DB::transaction(function () use($args){
                $members = $this->memberRepo->deleteContact_Project($args['project_id'], $args['contact_id']);
                return $args['contact_id'];
            }, 3);
        }
        catch (\Illuminate\Database\QueryException $e)
        {
			return [
                'contact_id' => null,
                'message' => 'Este contacto no se puede eliminar(Tiene transacciones registradas, intente eliminar por Role)',
                'type' => 'Failed'
            ];
        }
        return [
            'contact_id' => $mem,
            'message' => 'Contacto eliminado exitosamente',
            'type' => 'Successful'
        ];
    }
}
