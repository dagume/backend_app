<?php

namespace App\GraphQL\Mutations;

use App\Activity;
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
            $fileMetadata = new \Google_Service_Drive_DriveFile([
                'name'     => $args['name'],
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$this->folder_id ],
            ]);
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
            $folder = Conection_Drive()->files->create($fileMetadata, ['fields' => 'id']);
            $activity->drive_id             = $folder->id;
            $activity->save();
        }, 3);
        return [
            'message' => 'Actividad creada'
        ];
    }
}
