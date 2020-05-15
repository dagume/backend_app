<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ActivityRepository;
use App\Repositories\ProjectRepository;
use DB;

class UpdateActivity
{
    protected $activityRepo;
    protected $projectRepo;


    public function __construct(ActivityRepository $actRepo, ProjectRepository $proRepo)
    {
        $this->activityRepo = $actRepo;
        $this->projectRepo = $proRepo;
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
        $act = $this->activityRepo->find($args['id']);
        $start_date_project = $this->projectRepo->find($act->project_id)->start_date;
        $end_date_project = $this->projectRepo->find($act->project_id)->end_date;

        if (empty($args['date_start']) || is_null($args['date_start']))
        {
            $start_date_activity = $act->date_start;
        }else {
            $start_date_activity = $args['date_start'];
        }
        if (empty($args['date_end']) || is_null($args['date_end']))
        {
            $end_date_activity = $act->date_end;
        }else {
            $end_date_activity = $args['date_end'];
        }

        if($start_date_activity > $start_date_project || $args['is_added'] === True || $args['is_act'] === True)
        {
            if($end_date_project > $end_date_activity || $args['is_added'] === True || $args['is_act'] === True)
            {
                try
		        {
                    $activity = $this->activityRepo->update($args['id'], $args);
		        }
                catch (\Exception $e)
                {
		        	return [
                        'activity' => null,
                        'message' => 'Error, no se pudo editar. Vuelva a intentar',
                        'type' => 'Failed'
                    ];
                }
                return [
                    'activity' => $activity,
                    'message' => 'Actividad actualizada Exitosamente',
                    'type' => 'Successful'
                ];
            }else{
                return [
                    'activity' => null,
                    'message' => 'La fecha final de la actividad es mayor a la fecha de terminacion del proyecto.',
                    'type' => 'Failed'
                ];
            }
        }else{
            return [
                'activity' => null,
                'message' => 'La fecha inicial de la actividad es menor a la de inicio del proyecto.',
                'type' => 'Failed'
            ];
        }

    }
}
