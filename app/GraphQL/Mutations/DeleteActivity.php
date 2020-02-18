<?php

namespace App\GraphQL\Mutations;

use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;
use App\Repositories\ActivityRepository;
use App\Repositories\Document_referenceRepository;


class DeleteActivity
{

    protected $activityRepo;
    protected $document_referenceRepo;

    public function __construct(ActivityRepository $actRepo, Document_referenceRepository $doc_refRepo)
    {
        $this->activityRepo = $actRepo;
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
        try
		{
            $act = DB::transaction(function () use($args){
                $activity = $this->activityRepo->find($args['id']); //consultamos la data de la actividad
                //Consultamos el registro del documento en el drive
                $doc_ref = $this->document_referenceRepo->getFolderSubActivity($activity->project_id, $activity->id); 
                $this->activityRepo->delete($activity); // Eliminamos actividad en DB
                //Eliminamos carpeta raiz de dicha actividad(Se elimina todo lo que este dentro de esa carpeta)
                $activity_folder = Conection_Drive()->files->delete($doc_ref->drive_id);  
                return $activity;
            }, 3);    
		}
        catch (Exception $e)
        {
			return [
                'activity' => null,
                'message' => 'No se puede eliminar la actividad, intente más tarde'
            ];
        }    
        return [
            'activity' => $act,
            'message' => 'Actividad eliminada exitosamente'
        ];
    }
}
        