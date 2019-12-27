<?php

namespace App\GraphQL\Mutations;

use App\Document_reference;
use App\Project;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;
use App\Repositories\ProjectRepository;
use App\Repositories\Document_referenceRepository;

class CreateProject
{
    protected $projectRepo;
    protected $documentRepo;

    public function __construct(ProjectRepository $proRepo, Document_referenceRepository $docRepo){
        $this->projectRepo = $proRepo;
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
        //Transaccion para create
        DB::transaction(function () use($args){
            //buscamos el id del projecto para asignarlo a su nombre en DRIVE
            $id = $this->projectRepo->lastProject()->id + 1;
            if ($args['type'] = 0) {
                $folder_name = $id.'_pub_' . $args['name'];
            }else {
                $folder_name = $id.'_priv_' . $args['name'];
            }
            if ($args['parent_project_id'] != null) {
                //hacemos conexion con el drive y creamos el folder. Metodos en Helper.php
            $project_folder = Conection_Drive()->files->create(Create_Folder($folder_name, $this->documentRepo->getFolderActYear()->drive_id), ['fields' => 'id']);
            $args['folder_id'] = $project_folder->id; //Id del folder que se creo en drive
            }
            $project = $this->projectRepo->create($args); //guarda registro del nuevo proyecto

            if ($args['parent_project_id'] != null) {
            $doc_ref_project = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_project->parent_document_id = DB::table('document_reference')->where('name', date("Y"))->first()->id;
            $doc_ref_project->name = $args['name'];
            $doc_ref_project->type = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_project->project_id = Project::max('id');
            $doc_ref_project->module_id = 2; //id 2 pertenece al modulo Project
            $doc_ref_project->drive_id = $project_folder->id;
            $doc_ref_project->save();
            //hacemos conexion con el drive y creamos el folder. Metodos en Helper.php
            $activity_folder = Conection_Drive()->files->create(Create_Folder('Actividades', $project_folder->id), ['fields' => 'id']);
            $doc_ref_activity = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_activity->parent_document_id = DB::table('document_reference')->where('name', $args['name'])->first()->id;
            $doc_ref_activity->name ='Actividades';
            $doc_ref_activity->type = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_activity->project_id = Project::max('id');
            $doc_ref_activity->drive_id = $activity_folder->id;
            $doc_ref_activity->save();
            //hacemos conexion con el drive y creamos el folder. Metodos en Helper.php
            $account_folder = Conection_Drive()->files->create(Create_Folder('Contabilidad', $project_folder->id), ['fields' => 'id']);
            $doc_ref_account = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_account->parent_document_id = DB::table('document_reference')->where('name', $args['name'])->first()->id;
            $doc_ref_account->name ='Contabilidad';
            $doc_ref_account->type = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_account->project_id = Project::max('id');
            $doc_ref_account->drive_id = $account_folder->id;
            $doc_ref_account->save();
            }
        }, 3);
        return [
            'message' => 'Proyecto creado exitosamente'
        ];
    }
}
