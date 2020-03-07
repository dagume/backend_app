<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;

class Member_roles
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
        $MemberRoles = DB::select('Select * from crosstab(\'SELECT c.id, r.id, r.id FROM contacts c INNER JOIN members m ON c.id = m.contact_id INNER JOIN roles r ON r.id = m.role_id where m.project_id = '.$args['project_id'].'  ORDER BY 1,2\')
        AS final_result(contact_id integer, role1 integer, role2 integer, role3 integer, role4 integer, role5 integer, role6 integer, role7 integer, role8 integer)');

        return $MemberRoles;
    }
}
