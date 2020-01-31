<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;

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
        $contact = DB::select('select *
        from  
            (select con.folder_id, doc_mem.created_at, doc_mem.updated_at, doc_mem.doc_id, doc_mem.file_id
                from documents_member as doc_mem 
                inner join members as mem
                on   mem.id = doc_mem.member_id
                inner join contacts as con
                on   con.id = mem.contact_id		    
                where mem.contact_id = ? and mem.project_id = ?
            ) as documentos_integrante
            right join 
            (select rol.name as role_name, documents_rol.id as documento_id, name_required_documents
                from documents_rol 
                inner join required_documents 
                on required_documents.id = documents_rol.required_document_id
                inner join roles as rol
                on rol.id = documents_rol.role_id
                where documents_rol.role_id in (select role_id from members where contact_id = ? and project_id = ?)
            ) as documentos_requeridos
            on documentos_integrante.doc_id = documentos_requeridos.documento_id', [$args['contact_id'] , $args['project_id'], $args['contact_id'] , $args['project_id']]);
        
        return $contact;      
    }
}
