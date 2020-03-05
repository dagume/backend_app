<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Accounting_movementRepository;
use DB;
class Create_movement
{
    protected $accountRepo;

    public function __construct(Accounting_movementRepository $acoRepo)
    {
        $this->accountRepo = $acoRepo;
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
            $movement = $this->accountRepo->create($args);
            return $movement;
        }, 3);
        return [
            'accounting_movement' => $mov,
            'message' => "Movimiento registrado"
        ];
    }
}
