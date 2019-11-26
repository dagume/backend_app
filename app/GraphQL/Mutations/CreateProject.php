<?php

namespace App\GraphQL\Mutations;

use App\Document_reference;
use App\Project;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;


class CreateProject
{
    protected $folder_id    = '1bMApYJYghY6pFbNctOCQ9eFoARq8m20u';

    public function __construct(){}
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

            $project = new Project; //Capturando datos del proyecto
            $project->project_type_id       =$args['project_type_id'];
            $project->parent_project_id     =$args['parent_project_id'];
            $project->name                  =$args['name'];
            $project->start_date            =$args['start_date'];
            $project->end_date              =$args['end_date'];
            $project->description           =$args['description'];
            $project->contract_value        =$args['contract_value'];
            $project->state                 =$args['state'];
            $project->place                 =$args['place'];
            $project->address               =$args['address'];
            $project->type                  =$args['type'];
            $project->association           =$args['association'];
            $project->consortium_name       =$args['consortium_name'];
            //hacemos conexion con el drive y creamos el folder. Metodos en Helper.php
            $project_folder = Conection_Drive()->files->create(Create_Folder($args['name'], DB::table('document_reference')->where('name', date("Y"))->first()->drive_id), ['fields' => 'id']);
            $project->folder_id = $project_folder->id; //Id del folder que se creo en drive
            $project->save();

            $doc_ref_project = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_project->parent_document_id = DB::table('document_reference')->where('name', date("Y"))->first()->id;
            $doc_ref_project->name = $args['name'];
            $doc_ref_project->type = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_project->project_id = Project::max('id');
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
        }, 3);
        return [
            'message' => 'Proyecto creado exitosamente'
        ];
    }
}
