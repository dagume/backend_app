<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Order;
use App\Mail\RequestForQuotation;
use App\Quotation;
use Illuminate\Support\Facades\Mail;
use GraphQL\Type\Definition\ResolveInfo;
use DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateApplication
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
        DB::transaction(function () use($args){
            //dd(User::find(1)->email);
            $order = new Order;
            $order->name                = $args['name'];
            $order->code                = $args['code'];
            $order->application_date    = now();
            //$order->state             = $args['state'];
            $order->description         = $args['description'];
            $order->__delivery_site     = $args['__delivery_site'];
            $order->sender_data__       = auth()->user()->id_contact;
            $order->save();

            $emails = $args['email_contacts'];
            foreach ($emails as $ema ) {
                Mail::to(User::find($ema)->email)->send(new RequestForQuotation(User::find(2)));
                $quotation = new Quotation;
                $quotation->id_order = (int) Order::max('id_order');
                $quotation->id_contact = $ema;
                $quotation->save();
            }
        }, 3);

        
        return [
            'message' => 'Solicitud Enviada correctamente'
        ];
    }
}
