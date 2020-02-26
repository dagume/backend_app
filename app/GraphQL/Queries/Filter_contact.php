<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\User;
use DB;

class Filter_contact
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
        global $query;
        $contacts = new User;
        $query = trim($args['searchText']);
        $contacts = DB::table('contacts')
            ->where('state','=', 1)
            ->where(function ($que) {
                global $query;
                $que->where(function ($que1) {
                    global $query;
                    $que1->where('lastname','ILIKE',$query)
                ->orwhere('identification_number','ILIKE',$query);
                })
                ->orwhere('name','ILIKE',$query);
            })
            ->get();
        return $contacts;


    }
}
