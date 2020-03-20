<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Document_referenceRepository;
use DB;

class UpdateRequiredDocument
{
    protected $document_referenceRepo;

    public function __construct(Document_referenceRepository $doc_refRepo)
    {
        $this->document_referenceRepo = $doc_refRepo;
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
        $docReference = $this->document_referenceRepo->getDocumentRequired($args['contact_id'], $args['doc_id']);
        $docReference['drive_id'] = $args['drive_id'];
        ////////////////////////////
        /////Falta eliminar el archivo que esta en drive
        ////////////////////////////
        $this->document_referenceRepo->update($docReference->id, $docReference);
    }
}
