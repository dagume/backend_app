<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;

class Pucs_ThirdLevel
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
        $Pucs = DB::select('select firstLevel.id as firstlevel_id, firstLevel.name as firstlevel_name, secondLevel.id as secondlevel_id, secondLevel.name as secondlevel_name, thirdLevel.id as thirdlevel_id, thirdLevel.name as thirdlevel_name from(
            select * from puc where parent_puc_id is null
        ) as firstLevel 
        inner join (select * from puc where parent_puc_id is not null) as secondLevel on firstLevel.id = secondLevel.parent_puc_id  
        inner join (select * from puc where parent_puc_id is not null) as thirdLevel on secondLevel.id = thirdLevel.parent_puc_id');
        //dd($Pucs);
        return $Pucs; 
    }
}
