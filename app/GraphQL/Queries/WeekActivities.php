<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class WeekActivities
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
        $semana=date("W");
        $diaSemana = date("w");
        if ($diaSemana == 0){
            $diaSemana = 7;
        }
        $primer_dia = date("d-m-Y", mktime(0, 0, 0, date("m")  , date("d")-$diaSemana+1, date("Y")));
        $ultimo_dia = date("d-m-Y", mktime(0, 0, 0, date("m")  , date("d")+(7 - $diaSemana), date("Y")));
        //dd($ultimo_dia);
        return null;
    }
}
