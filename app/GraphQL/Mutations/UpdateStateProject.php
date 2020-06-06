<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ProjectRepository;
use App\Repositories\ActivityRepository;
use App\Repositories\Document_referenceRepository;
use App\Document_reference;

use DB;

class UpdateStateProject
{
    protected $projectRepo;
    protected $activityRepo;
    protected $documentRepo;

    public function __construct(Document_referenceRepository $docRepo, ActivityRepository $actRepo, ProjectRepository $proRepo)
    {
        $this->projectRepo = $proRepo;
        $this->activityRepo = $actRepo;
        $this->documentRepo = $docRepo;
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
        //dd($this->projectRepo->find($args['id'])->state == 4);
        try
		{
            //Folder de actividades dentro del proyecto
            $activity_folder = $this->documentRepo->getFolderParentActivity($args['id']);
            if ($this->projectRepo->find($args['id'])->state == 4 && $args['state'] !== 4) {
                //Hacemos conexion con el drive y creamos el folder de Actividad. Metodos en Helper.php
                $again_Act_folder = Conection_Drive()->files->create(Create_Folder('Acta de reinicio '.date("d-m-Y"), $activity_folder->drive_id), ['fields' => 'id']);
                //guarda registro de la nueva actividad
                $activity_again = $this->activityRepo->create(StoreActivity($args['id'], 'Acta de reinicio '.date("d-m-Y"), $again_Act_folder->id));

                $doc_ref_activity = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
                $doc_ref_activity->parent_document_id = $this->documentRepo->getFolderParentActivity($args['id'])->id;    //Buscamos el folder donde vamos a guardar la actividad
                $doc_ref_activity->name ='Acta de reinicio '.date("d-m-Y");
                $doc_ref_activity->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_activity->activity_id =  $activity_again->id;
                $doc_ref_activity->project_id = $args['id'];
                $doc_ref_activity->module_id = 1; //id 1 pertenece al modulo Activity
                $doc_ref_activity->drive_id = $again_Act_folder->id;
                $doc_ref_activity->save();

                //Hacemos conexion con el drive y creamos el folder de Actividad. Metodos en Helper.php
                $poliza_Act_folder = Conection_Drive()->files->create(Create_Folder('Polizas de reinicio '.date("d-m-Y"), $activity_folder->drive_id), ['fields' => 'id']);
                //guarda registro de la nueva actividad
                $activity_poliza = $this->activityRepo->create(StoreActivity($args['id'], 'Polizas de reinicio '.date("d-m-Y"), $poliza_Act_folder->id));

                $doc_ref_activity = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
                $doc_ref_activity->parent_document_id = $this->documentRepo->getFolderParentActivity($args['id'])->id;    //Buscamos el folder donde vamos a guardar la actividad
                $doc_ref_activity->name ='Polizas de reinicio '.date("d-m-Y");
                $doc_ref_activity->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_activity->activity_id =  $activity_poliza->id;
                $doc_ref_activity->project_id = $args['id'];
                $doc_ref_activity->module_id = 1; //id 1 pertenece al modulo Activity
                $doc_ref_activity->drive_id = $poliza_Act_folder->id;
                $doc_ref_activity->save();
            }

            if ($args['state'] === 4 && $this->projectRepo->find($args['id'])->state != 4) {
                //Hacemos conexion con el drive y creamos el folder de Actividad. Metodos en Helper.php
                $suspensionAct_folder = Conection_Drive()->files->create(Create_Folder('Acta de suspensión '.date("d-m-Y"), $activity_folder->drive_id), ['fields' => 'id']);
                //guarda registro de la nueva actividad
                $activity = $this->activityRepo->create(StoreActivity($args['id'], 'Acta de suspensión '.date("d-m-Y"), $suspensionAct_folder->id));

                $doc_ref_activity = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
                $doc_ref_activity->parent_document_id = $this->documentRepo->getFolderParentActivity($args['id'])->id;    //Buscamos el folder donde vamos a guardar la actividad
                $doc_ref_activity->name ='Acta de suspensión '.date("d-m-Y");
                $doc_ref_activity->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_activity->activity_id =  $activity->id;
                $doc_ref_activity->project_id = $args['id'];
                $doc_ref_activity->module_id = 1; //id 1 pertenece al modulo Activity
                $doc_ref_activity->drive_id = $suspensionAct_folder->id;
                $doc_ref_activity->save();
            }

            $project = $this->projectRepo->update($args['id'], $args);
		}
        catch (\Exception $e)
        {
			return [
                'project' => null,
                'message' => 'Error, no se pudo cambiar el estado del proyecto. Vuelva a intentar',
                'type' => 'Failed'
            ];
        }
        return [
            'project' => $project,
            'message' => 'El Proyecto cambio de estado',
            'type' => 'Successful'
        ];
    }
}
