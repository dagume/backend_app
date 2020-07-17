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
        $contact = auth()->user(); //usuario logueado
        //DD($contact->id);
        foreach ($contact->roles as $rol) { //Verificacomos si este usuario tiene role administrador
            if (trim($rol->special) === 'all-access') {
                $Pucs = DB::select('select firstLevel.id as firstlevel_id, firstLevel.name as firstlevel_name, secondLevel.id as secondlevel_id, secondLevel.name as secondlevel_name, thirdLevel.id as thirdlevel_id, thirdLevel.name as thirdlevel_name from(
                    select * from puc where parent_puc_id is null
                ) as firstLevel 
                inner join (select * from puc where parent_puc_id is not null) as secondLevel on firstLevel.id = secondLevel.parent_puc_id  
                inner join (select * from puc where parent_puc_id is not null) as thirdLevel on secondLevel.id = thirdLevel.parent_puc_id');
                
                return $Pucs;
            }
        }
        $Pucs = DB::select('select distinct puc_role.puc_id as distincts, pucsenables.* from (
            select firstLevel.id as firstlevel_id, firstLevel.name as firstlevel_name, secondLevel.id as secondlevel_id, secondLevel.name as secondlevel_name, thirdLevel.id as thirdlevel_id, thirdLevel.name as thirdlevel_name 
            from(
                select * from puc where parent_puc_id is null
             ) as firstLevel 
                inner join (select * from puc where parent_puc_id is not null) as secondLevel on firstLevel.id = secondLevel.parent_puc_id  
                inner join (select * from puc where parent_puc_id is not null) as thirdLevel on secondLevel.id = thirdLevel.parent_puc_id
        ) as pucsenables
                inner join puc_role on pucsenables.thirdlevel_id = puc_role.puc_id
                where puc_role.role_id in (select role_id from members where contact_id = ? and project_id = ?)',[$contact->id, $args['project_id']]);
        
        return $Pucs;
    }
}
