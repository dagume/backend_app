<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;

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
        $week=date("W");
        $day_week = date("w");

        if ($day_week == 0){
            $day_week = 7;
        }
        $first_day = '\''.date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-$day_week+1, date("Y"))).'\'';
        $last_day = '\''.date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+(7 - $day_week), date("Y"))).'\'';
        $activities = DB::select('select * from activities where date_end between ? and ?',[$first_day, $last_day]);
        //dd($ultimo_dia);
        return $activities;
    }
}