<?php

namespace App\GraphQL\Mutations;

use App\Activity;
use App\Document_reference;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Document_referenceRepository;
use App\Repositories\ActivityRepository;
use DB;

class CreateActivity
{
    protected $documentRepo;
    protected $activityRepo;

    public function __construct(Document_referenceRepository $docRepo, ActivityRepository $actRepo){
        $this->documentRepo = $docRepo;
        $this->activityRepo = $actRepo;
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
        DB::transaction(function () use($args){
            //verifica si la actividad es padre o hija para asi saber donde crear el folder
            if ($args['parent_activity_id'] == null) {
                //hace conexion con el drive y crea el folder. Metodos en Helper.php                
                $activity_folder = Conection_Drive()->files->create(Create_Folder($args['name'], $this->documentRepo->getFolderParentActivity($args['project_id'])->drive_id), ['fields' => 'id']);
                $args['parent_document_id'] = $this->documentRepo->getFolderParentActivity($args['project_id'])->id;
                $args['type']               = 1; // 0 = Tipo File, 1 = Tipo Folder
                $args['activity_id']        = $this->activityRepo->lastActivity()->id + 1;
                $args['module_id']          = 1; //id 1 pertenece al modulo Activity
                $args['drive_id']           = $activity_folder->id;                
            }else {
                //hace conexion con el drive y crea el folder. Metodos en Helper.php y                
                $activity_folder = Conection_Drive()->files->create(Create_Folder($args['name'], $this->documentRepo->getFolderSubActivity($args['project_id'], $args['parent_activity_id'])->drive_id), ['fields' => 'id']);
                $doc_reference = new Document_reference;
                $args['parent_document_id'] = $this->documentRepo->getFolderSubActivity($args['project_id'], $args['parent_activity_id'])->id;
                $args['type']               = 1; // 0 = Tipo File, 1 = Tipo Folder
                $args['activity_id']        = $this->activityRepo->lastActivity()->id + 1;
                $args['module_id']          = 1; //id 1 pertenece al modulo Activity
                $args['drive_id']           = $activity_folder->id;
            }
                $activity = $this->activityRepo->create($args); //guarda registro de la nueva actividad
                $doc_reference = $this->documentRepo->create($args); //guarda registro del nuevo documentReference
        }, 3);
        return [
            'message' => 'Actividad creada'
        ];
    }
}
