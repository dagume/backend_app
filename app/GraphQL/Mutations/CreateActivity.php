<?php

namespace App\GraphQL\Mutations;

use App\Activity;
use App\Document_reference;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;

class CreateActivity
{

    protected $folder_id    = '1bMApYJYghY6pFbNctOCQ9eFoARq8m20u';

    public function __construct(){
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

            $activity = new Activity;
            $activity->project_id           = $args['project_id'];
            $activity->parent_activity_id   = $args['parent_activity_id'];
            $activity->name                 = $args['name'];
            $activity->description          = $args['description'];
            $activity->date_start           = $args['date_start'];
            $activity->date_end             = $args['date_end'];
            $activity->state                = $args['state'];
            $activity->completed            = $args['completed'];
            $activity->priority             = $args['priority'];
            $activity->notes                = $args['notes'];
            $activity->amount               = $args['amount'];
            $activity->is_added             = $args['is_added'];
            $activity->is_folder            = $args['is_folder'];
            //hacemos conexion con el drive y creamos el folder. Metodos en Helper.php
            $activity_folder = Conection_Drive()->files->create(Create_Folder($args['name'], DB::table('document_reference')->where('project_id', $args['project_id'])->where('name', 'Actividades')->first()->drive_id), ['fields' => 'id']);
            $activity->drive_id             = $activity_folder->id;
            $activity->save();


            $doc_reference = new Document_reference;
            $doc_reference->parent_document_id = DB::table('document_reference')->where('project_id', $args['project_id'])->where('name', 'Actividades')->first()->id;
            $doc_reference->name            = $args['name'];
            $doc_reference->activity_id      = Activity::max('id');
            $doc_reference->project_id      = $args['project_id'];
            $doc_reference->project_id      = $args['project_id'];
            $doc_reference->module_id = 1; //id 1 pertenece al modulo Activity
            $doc_reference->drive_id        = $activity_folder->id;
            $doc_reference->save();
        }, 3);
        return [
            'message' => 'Actividad creada'
        ];
    }
}
