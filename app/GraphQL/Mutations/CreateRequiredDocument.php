<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\RequiredDocumentRepository;
use DB;

class CreateRequiredDocument
{
    protected $requiredDocumentRepo;

    function __construct(RequiredDocumentRepository $req_docRepo)
    {
        $this->requiredDocumentRepo = $req_docRepo;
    }
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
        $req_doc = DB::transaction(function () use($args){
            $required_document = $this->requiredDocumentRepo->create($args); //guarda registro del nuevo documento requerido
            return $required_document;
        }, 3);
            return [
                'required_documents'=> $req_doc,
                'message' => 'Documento requerido creado exitosamente',
                'type' => 'Successful'
            ];    }
}
