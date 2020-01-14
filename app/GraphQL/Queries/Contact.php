<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Contact
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
        $contact = DB::select('SELECT c.name, r.name_required_documents
        FROM documents_member as d
        INNER JOIN members as m ON m.id = d.member_id
        INNER JOIN contacts as c ON c.id = m.contact_id
        INNER JOIN documents_rol as dr ON dr.id = d.doc_id
        INNER JOIN required_documents as r ON r.id = dr.required_document_id
        where c.id = 1');
        
        return $contact;      
    }
}
