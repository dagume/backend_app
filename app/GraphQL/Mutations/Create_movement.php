<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Accounting_movementRepository;
use App\Repositories\ContactRepository;
use DB;

class Create_movement
{
    protected $accountRepo;
    protected $contactRepo;

    public function __construct(ContactRepository $conRepo, Accounting_movementRepository $acoRepo)
    {
        $this->accountRepo = $acoRepo;
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
        $mov = DB::transaction(function () use($args){
            $contact_origin = $this->contactRepo->find($args['origin_id']);
            $contact_destination = $this->contactRepo->find($args['destination_id']);
            if ($contact_origin->identification_number == $args['project_id'] || $contact_destination->identification_number == $args['project_id']) {
                $args['registration_date'] = now();
                $args['sender_id'] = auth()->user()->id;
                $args['state_movement'] = True;
                if ($args['origin_id'] == $args['destination_id']) {// si origen y destino son iguales, el destino se deja nulo para luego poder generar reportes sin error
                    $args['destination_id'] = null;
                    $args['destination_role_id'] = null;
                }
                $movement = $this->accountRepo->create($args); //registramos el movimiento
                //Saber si es prestamo o pago entre proyectos
                if (!is_null($args['destination_id'])) { //si no es nulo quiere decir que si tiene un destino el movimiento
                    if ($contact_origin->type == 0 && $contact_destination->type == 0 ) { //Si los dos contactos son tipo proyecto se debe crear doble registro
                        if ($args['project_id'] == $contact_origin->identification_number) {// verificamos en que proyecto deberia quedar guardado el segundo registro
                            $args['project_id'] = $contact_destination->identification_number;
                        }else {
                            $args['project_id'] = $contact_origin->identification_number;
                        }
                        $movement = $this->accountRepo->create($args);
                    }
                }
            }else {
                return [
                    'accounting_movement' => null,
                    'message' => "Ese tipo de movimiento no se puede realizar"
                ];
            }
            return [
                'accounting_movement' => $movement,
                'message' => "Movimiento registrado"
            ];
        }, 3);
        return $mov;
    }
}
