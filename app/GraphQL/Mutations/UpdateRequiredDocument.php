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
        try {
            $mess = DB::transaction(function () use($args){
                $docReference = $this->document_referenceRepo->getDocumentRequired($args['contact_id'], $args['doc_id']); //buscamos el registro del documento requerido
                if (!empty($docReference)) {//Si existe algun regstro debemos actualizarlo
                    $documentReference['drive_id'] = $args['drive_id'];
                    $this->document_referenceRepo->update($docReference->id, $documentReference); //Guardamos el nuevo drive_id
                    Conection_Drive()->files->delete($docReference->drive_id); //eliminamos el archivo en el drive
                    return $message = 'Documento actualizado exitosamente';
                }else{
                    return $message = 'No hay documento guardado';
                }
            }, 3);
        } catch (Exception $e) {
            return [
                'message' => 'No se puedo actualizar el documento, vuelvalo a intentar'
            ];
        }
        return [
            'message' => $mess
        ];
    }
}
