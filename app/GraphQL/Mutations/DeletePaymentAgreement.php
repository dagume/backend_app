<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\PaymentAgreement;
use DB;

class DeletePaymentAgreement
{
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
        //dd(PaymentAgreement::find($args['id'])->state);
        if (PaymentAgreement::find($args['id'])->state === false) {
            try
		    {
		    	$paymentAgreement = PaymentAgreement::find($args['id']);
		    	$paymentAgreement->delete();
		    }
            catch (\Illuminate\Database\QueryException $e)
            {
		    	return [
                    'paymentAgreement' => null,
                    'accounting_movement' => null,
                    'message' => 'Esta cuenta no se puede eliminar',
                    'type' => 'Failed'
                ];
            }
            return [
                'paymentAgreement' => $paymentAgreement,
                'accounting_movement' => null,
                'message' => 'Cuenta eliminada exitosamente',
                'type' =>'Successful'
            ];
        }else {
            return [
                'paymentAgreement' => null,
                'accounting_movement' => null,
                'message' => 'El acuerdo de pago no se puede eliminar. ya fue pagado',
                'type' => 'Failed'
            ];

        }
    }
}
