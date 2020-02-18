<?php

namespace App\GraphQL\Mutations;

use Caffeinated\Shinobi\Models\Role;
use App\Document_reference;
use App\Project;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;
use App\Repositories\ProjectRepository;
use App\Repositories\Document_referenceRepository;
use App\Repositories\ActivityRepository;
use App\Repositories\ContactRepository;
use App\Repositories\MemberRepository;

class CreateProject
{
    protected $projectRepo;
    protected $documentRepo;
    protected $activityRepo;
    protected $contactRepo;
    protected $memberRepo;

    public function __construct(ProjectRepository $proRepo, Document_referenceRepository $docRepo, ActivityRepository $actRepo, ContactRepository $conRepo, MemberRepository $memRepo){
        $this->projectRepo = $proRepo;
        $this->documentRepo = $docRepo;
        $this->activityRepo = $actRepo;
        $this->contactRepo = $conRepo;
        $this->memberRepo = $memRepo;
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
        //dd(DB::select('select id from roles where name = ?', ['Proyecto'])[0]->id);
        //Transaccion para create
        $proj = DB::transaction(function () use($args){
            //buscamos el id del projecto para asignarlo a su nombre en DRIVE
            $id = $this->projectRepo->lastProject()->id + 1;
            if ($args['type'] == 0) {
                $folder_name = $id.'_pub_' . $args['name'];
            }else {
                $folder_name = $id.'_priv_' . $args['name'];
            }
            if ($args['parent_project_id'] != null) {
                //hacemos conexion con el drive y creamos el folder. Metodos en Helper.php
            $project_folder = Conection_Drive()->files->create(Create_Folder($folder_name, $this->documentRepo->getFolderActYear()->drive_id), ['fields' => 'id']);
            $args['folder_id'] = $project_folder->id; //Id del folder que se creo en drive
            }
            $someJSON = json_encode($args['place']);
            $args['place'] = $someJSON;
            $project = $this->projectRepo->create($args); //guarda registro del nuevo proyecto

            //Creamos el contacto del proyecto para agregarlo como integrante y asi poderlo usar 
            //como una cuenta que recibe y trasnfiere dinero
            $con['name'] = $project->id.'_'.$project->name;
            $con['state'] = 0;
            $contact = $this->contactRepo->create($con);
            $member['project_id'] = $project->id;
            $member['contact_id'] = $contact->id;
            $member['role_id'] = DB::select('select id from roles where name = ?', ['Proyecto'])[0]->id;
            $this->memberRepo->create($member);

            if ($args['parent_project_id'] != null) {   //Si es proyecto padre no se le crea estructura de carpetas
            $doc_ref_project = new Document_reference;  // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_project->parent_document_id = DB::table('document_reference')->where('name', date("Y"))->first()->id;
            $doc_ref_project->name = $args['name'];
            $doc_ref_project->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_project->project_id = $project->id;
            $doc_ref_project->module_id = 2; //id 2 pertenece al modulo Project
            $doc_ref_project->drive_id = $project_folder->id;
            $doc_ref_project->save();

            //Hacemos conexion con el drive y creamos el folder Actividades. Metodos en Helper.php
            $activity_folder = Conection_Drive()->files->create(Create_Folder('Actividades', $project_folder->id), ['fields' => 'id']);
            $doc_ref_activity = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_activity->parent_document_id = DB::table('document_reference')->where('name', $args['name'])->first()->id;
            $doc_ref_activity->name ='Actividades';
            $doc_ref_activity->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_activity->project_id = $project->id;
            $doc_ref_activity->module_id = 2; //id 2 pertenece al modulo Project
            $doc_ref_activity->drive_id = $activity_folder->id;
            $doc_ref_activity->save();

            if ($args['association'] == 0) {    // si es consorcio se le crea actividad de acta consorcial
                //Hacemos conexion con el drive y creamos el folder de Actividad. Metodos en Helper.php
                $consortium_folder = Conection_Drive()->files->create(Create_Folder('Acta Consorcial', $activity_folder->id), ['fields' => 'id']);
                //guarda registro de la nueva actividad
                $activity = $this->activityRepo->create(StoreActivity($project->id, 'Acta Consorcial',$consortium_folder->id));

                $doc_ref_activity = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
                $doc_ref_activity->parent_document_id = $this->documentRepo->getFolderParentActivity($project->id)->id;    //Buscamos el folder donde vamos a guardar la actividad
                $doc_ref_activity->name ='Acta Consorcial';
                $doc_ref_activity->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_activity->activity_id =  $activity->id;
                $doc_ref_activity->project_id = $project->id;
                $doc_ref_activity->module_id = 1; //id 1 pertenece al modulo Activity
                $doc_ref_activity->drive_id = $consortium_folder->id;
                $doc_ref_activity->save();
            }

            if ($args['type'] == 0) {    // si es Publico se le crea actividad de Propuesta
                //Hacemos conexion con el drive y creamos el folder de Actividad. Metodos en Helper.php
                $proposal_folder = Conection_Drive()->files->create(Create_Folder('Propuesta', $activity_folder->id), ['fields' => 'id']);
                //guarda registro de la nueva actividad
                $activity = $this->activityRepo->create(StoreActivity($project->id, 'Propuesta',$proposal_folder->id)); //guarda registro de la nueva actividad

                $doc_ref_activity = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
                $doc_ref_activity->parent_document_id = $this->documentRepo->getFolderParentActivity($project->id)->id;    //Buscamos el folder donde vamos a guardar la actividad
                $doc_ref_activity->name ='Propuesta';
                $doc_ref_activity->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_activity->activity_id =  $activity->id;
                $doc_ref_activity->project_id = $project->id;
                $doc_ref_activity->module_id = 1; //id 1 pertenece al modulo Activity
                $doc_ref_activity->drive_id = $proposal_folder->id;
                $doc_ref_activity->save();
            }
            if ($args['project_type_id'] == 1) {    // si es Publico se le crea actividad de Propuesta
                //Hacemos conexion con el drive y creamos el folder de Actividad. Metodos en Helper.php
                $build_folder = Conection_Drive()->files->create(Create_Folder('Certificado Sena y Comfaboy', $activity_folder->id), ['fields' => 'id']);
                //guarda registro de la nueva actividad
                $activity = $this->activityRepo->create(StoreActivity($project->id, 'Certificado Sena y Comfaboy',$build_folder->id)); //guarda registro de la nueva actividad

                $doc_ref_activity = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
                $doc_ref_activity->parent_document_id = $this->documentRepo->getFolderParentActivity($project->id)->id;    //Buscamos el folder donde vamos a guardar la actividad
                $doc_ref_activity->name ='Certificado Sena y Comfaboy';
                $doc_ref_activity->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_activity->activity_id =  $activity->id;
                $doc_ref_activity->project_id = $project->id;
                $doc_ref_activity->module_id = 1; //id 1 pertenece al modulo Activity
                $doc_ref_activity->drive_id = $build_folder->id;
                $doc_ref_activity->save();
            }

            //Hacemos conexion con el drive y creamos el folder de Contabilidad. Metodos en Helper.php
            $account_folder = Conection_Drive()->files->create(Create_Folder('Contabilidad', $project_folder->id), ['fields' => 'id']);
            $doc_ref_account = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_account->parent_document_id = DB::table('document_reference')->where('name', $args['name'])->first()->id;
            $doc_ref_account->name ='Contabilidad';
            $doc_ref_account->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_account->project_id = $project->id;
            $doc_ref_account->module_id = 2; //id 2 pertenece al modulo Project
            $doc_ref_account->drive_id = $account_folder->id;
            $doc_ref_account->save();

            //Hacemos conexion con el drive y creamos el folder de Contabilidad. Metodos en Helper.php
            $order_folder = Conection_Drive()->files->create(Create_Folder('Ordenes', $project_folder->id), ['fields' => 'id']);
            $doc_ref_account = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_account->parent_document_id = DB::table('document_reference')->where('name', $args['name'])->first()->id;
            $doc_ref_account->name ='Ordenes';
            $doc_ref_account->is_folder = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_account->project_id = $project->id;
            $doc_ref_account->module_id = 2; //id 2 pertenece al modulo Project
            $doc_ref_account->drive_id = $order_folder->id;
            $doc_ref_account->save();
            }
            return $project;
        }, 3);
        return [
            'project' => $proj,
            'message' => 'Proyecto creado exitosamente'
        ];
    }
}
