<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Accounting_movementRepository;
use App\Repositories\MemberRepository;
use DB;

class Create_transaction
{
    protected $accountRepo;
    protected $memberRepo;

    public function __construct(MemberRepository $memRepo, Accounting_movementRepository $acoRepo)
    {
        $this->accountRepo = $acoRepo;
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
        $mov = DB::transaction(function () use($args){
            $args['registration_date'] = now();
            $args['sender_id'] = auth()->user()->id;
            $args['state_movement'] = True;
            $args['puc_id']= 112010; //Revisar que cuenta del puc va (Cuentas por cobrar)
            $movement = $this->accountRepo->create($args);

            $mem = $this->memberRepo->getNameMember($args['destination_id']);
            $origin = $args['origin_id'];
            $destination = $args['destination_id'];

            $args['puc_id']= 2335; //Revisar que cuenta del puc va (Cuentas por pagar)
            $args['origin_id']= $destination;
            $args['destination_id']= $origin;
            $args['state_movement']= false;
            $args['payment_method']= null;
            $args['project_id'] = strstr($mem->name, '_', true); //sacamos el project_id del nombre del contacto
            $entry = $this->accountRepo->create($args);

            return $movement;
        }, 3);
        return [
            'accounting_movement' => $mov,
            'message' => "Movimiento registrado"
        ];
    }
}
