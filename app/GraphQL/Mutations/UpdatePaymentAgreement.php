<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\PaymentAgreementRepository;
use App\Repositories\OrderRepository;
use DB;

class UpdatePaymentAgreement
{
    protected $paymentRepo;
    protected $orderRepo;


    public function __construct(PaymentAgreementRepository $payRepo, OrderRepository $ordRepo)
    {
        $this->paymentRepo = $payRepo;
        $this->orderRepo = $ordRepo;
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
        $payment = $this->paymentRepo->find($args['id']);
        $order = $this->orderRepo->find($payment->order_id);
        $pending['pending_debt'] = $order->pending_debt - $args['amount']; //calculo de valor pendiente por pagar
        if ($pending['pending_debt'] >= 0) { //Validar que no de valor negativo

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
        }else {
            return [
                'paymentAgreement' => null,
                'accounting_movement' => null,
                'message' => "El valor a pagar no puede ser superior a la deuda",
                'type' => 'Failed'
            ];
        }
    }
}
