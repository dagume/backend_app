<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ActivityRepository;
use App\Repositories\ProjectRepository;
use App\GraphQL\Mutations\CreateActivity;
use App\Repositories\Accounting_movementRepository;
use DB;

class UpdateActivity
{
    protected $activityRepo;
    protected $projectRepo;
    protected $progress;
    protected $accountRepo;




    public function __construct(Accounting_movementRepository $acoRepo, ActivityRepository $actRepo, ProjectRepository $proRepo, CreateActivity $actProg)
    {
        $this->activityRepo = $actRepo;
        $this->projectRepo = $proRepo;
        $this->progress = $actProg;
        $this->accountRepo = $acoRepo;
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
        //dd($this->progress->missing_project_money($act->project_id));
        $start_date_project = $this->projectRepo->find($act->project_id)->start_date;
        $end_date_project = $this->projectRepo->find($act->project_id)->end_date;

        //Valida si envian fechas o no para que no se estalle
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
        if (empty($args['amount']) || is_null($args['amount']))
        {
            $amo = 0;
        }else {
            $amo = $args['amount'];
        }
        // Validacion de fechas que no se salgan de los rangos del ejecucion del proyecto
        if($start_date_activity >= $start_date_project || $act->is_act === True || $act->is_added === True)
        {
            if($end_date_project >= $end_date_activity || $act->is_act === True || $act->is_added === True)
            {
                // Si es adicional en tiempo o dinero solo debemos actualizar campos
                if ($act->is_add) {
                    //actualizamos los datos de la actividad
                    $activity = $this->activityRepo->update($args['id'], $args);
                    return [
                        'activity' => $activity,
                        'message' => 'Actividad actualizada Exitosamente',
                    'type' => 'Successful'
                    ];
                }      
                if ($amo <= $this->progress->missing_project_money($act->project_id)) //Validar que no exceda el faltante de recibir por el cliente, no puede entrar mas dinero del registrado en contrato
                {
                    try
    		        {
                        //actualizamos los datos de la actividad
                        $activity = $this->activityRepo->update($args['id'], $args);
                        //actualizamos el progreso del proyecto segun cantad de dinero ingresado por actas
                        if ($act->is_act) {
                            if (!empty($args['amount'])) {
                                $movement['value'] = $args['amount'];
                                $this->progress->Progress($act->is_act, $act->project_id); //actualizamos el progrso del proyecto
                                $this->accountRepo->update($this->accountRepo->getMovementAct($args['id'])->id, $movement);//Actualizamos el amount del movimiento
                            }
                        }
                    }
                    catch (\Exception $e)
                    {
                        return [
                            'activity' => null,
                            'message' => $e, 'Error, no se pudo editar. Vuelva a intentar',
                            'type' => 'Failed'
                        ];
                    }
                    return [
                        'activity' => $activity,
                        'message' => 'Actividad actualizada Exitosamente',
                    'type' => 'Successful'
                    ];
                }else {
                    return [
                        'activity' => null,
                        'message' => 'No puede ingresar más dinero del contratado en el proyecto, la sumatoria de las actas excede el valor total del contrato',
                        'type' => 'Failed'
                    ];
                }
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
