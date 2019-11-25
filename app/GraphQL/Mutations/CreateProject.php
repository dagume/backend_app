<?php

namespace App\GraphQL\Mutations;

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
        DB::transaction(function () use($args){
            $fileMetadata = new \Google_Service_Drive_DriveFile([
                'name'     => $args['name'],
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$this->folder_id ],
            ]);
            $project = new Project;
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
            $folder = Conection_Drive()->files->create($fileMetadata, ['fields' => 'id']);
            $project->folder_id         =$folder->id;
            $project->save();
        }, 3);
        return [
            'message' => 'Proyecto creado exitosamente'
        ];
    }
}
