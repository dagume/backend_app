<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\PaymentAgreementRepository;
use DB;

class UpdatePaymentAgreement
{
    protected $paymentRepo;

    public function __construct(PaymentAgreementRepository $payRepo){
        $this->paymentRepo = $payRepo;
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
        //dd($this->paymentRepo->find($args['id'])->state);
        if ($this->paymentRepo->find($args['id'])->state === false) {

            try
		    {
                $paymentAgrement = $this->paymentRepo->update($args['id'], $args);
		    }
            catch (\Exception $e)
            {
		    	return [
                    'paymentAgreement' => null,
                    'accounting_movement' => null,
                    'message' => 'Error, no se pudo editar. Vuelva a intentar',
                    'type' => 'Failed'
                ];
            }
            return [
                'paymentAgreement' => $paymentAgrement,
                'accounting_movement' => null,
                'message' => 'Acuerdo de pago actualizado Exitosamente',
                'type' => 'Successful'
            ];

        }else {
            return [
                'paymentAgreement' => null,
                'accounting_movement' => null,
                'message' => 'EL pago ya fue registrado no se puede modificar',
                'type' => 'Failed'
            ];
        }

    }
}