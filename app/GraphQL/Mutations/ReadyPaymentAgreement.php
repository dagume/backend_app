<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\PaymentAgreementRepository;
use App\Repositories\OrderRepository;
use App\Repositories\Order_documentRepository;
use App\Repositories\Accounting_movementRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ContactRepository;
use DB;

class ReadyPaymentAgreement
{
    protected $paymentRepo;
    protected $orderRepo;
    protected $ord_documentRepo;
    protected $accountRepo;
    protected $roleRepo;
    protected $contactRepo;

    public function __construct(ContactRepository $conRepo, RoleRepository $rolRepo, Accounting_movementRepository $acoRepo, Order_documentRepository $ord_docRepo, PaymentAgreementRepository $payRepo, OrderRepository $ordRepo)
    {
        $this->paymentRepo = $payRepo;
        $this->orderRepo = $ordRepo;
        $this->ord_documentRepo = $ord_docRepo;
        $this->accountRepo = $acoRepo;
        $this->roleRepo = $rolRepo;
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
        if ($this->paymentRepo->find($args['id'])->state === false) {
            $data = DB::transaction(function () use($args){  //se crea la transacion

                $getPayment = $this->paymentRepo->find($args['id']);//Buscamos el acueerdo de pago
                $order = $this->orderRepo->find($getPayment->order_id);//Buscamos la orden a la que correspode le acueerdo de pago

                $pending['pending_debt'] = $order->pending_debt - $args['amount'];//Resta de cuanto de debe a esa comprae

                if ($pending['pending_debt'] >= 0) { //Validar que no de valor negativo
                    $this->orderRepo->update($order->id, $pending);

                    $movement['puc_id'] = 110505;
                    $movement['project_id'] = $order->project_id;
                    $movement['destination_id'] = $order->contact_id;
                    $movement['destination_role_id'] = $this->roleRepo->getRolProveedor()->id;
                    $movement['origin_id'] = $this->contactRepo->getContactIdentificatioNumber($order->project_id)->id;
                    $movement['origin_role_id'] = $this->roleRepo->getRolProject()->id;
                    $movement['movement_date'] = now();
                    $movement['payment_method'] = $args['payment_method'];
                    $movement['value'] = $args['amount'];
                    $movement['code'] = $this->ord_documentRepo->getCodeOrderBuy($order->id)->code;
                    $movement['state_movement'] = True;
                    $movement['registration_date'] = now();
                    $movement['sender_id'] = auth()->user()->id;
                    $account_movement = $this->accountRepo->create($movement);

                    $pay['state'] = True;
                    $pay['amount'] = $args['amount'];
                    $payment = $this->paymentRepo->update($args['id'],$pay);
                    return [
                        'paymentAgreement' => $payment,
                        'accounting_movement' => $account_movement,
                        'message' => "Pago realizado con exito",
                        'type' => 'Successful'
                    ];
                }else {
                    return [
                        'paymentAgreement' => null,
                        'accounting_movement' => null,
                        'message' => "El valor a pagar no puede ser superior al valor de la deuda",
                        'type' => 'Failed'
                    ];
                }
            }, 3);
            return $data;
        }else {
            return [
                'paymentAgreement' => null,
                'accounting_movement' => null,
                'message' => 'EL pago ya fue registrado anteriormente',
                'type' => 'Failed'
            ];
        }
    }
}
