<?php

namespace App\GraphQL\Mutations;

use App\Activity;
use App\Document_reference;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Document_referenceRepository;
use App\Repositories\ActivityRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\Accounting_movementRepository;
use App\Repositories\ContactRepository;
use App\Repositories\RoleRepository;
use App\Repositories\MemberRepository;
use DB;

class CreateActivity
{
    protected $documentRepo;
    protected $activityRepo;
    protected $projectRepo;
    protected $accountRepo;
    protected $contactRepo;
    protected $roleRepo;
    protected $memberRepo;

    public function __construct(MemberRepository $memRepo, RoleRepository $rolRepo, ContactRepository $conRepo, Accounting_movementRepository $acoRepo, Document_referenceRepository $docRepo, ActivityRepository $actRepo, ProjectRepository $proRepo){
        $this->documentRepo = $docRepo;
        $this->activityRepo = $actRepo;
        $this->projectRepo = $proRepo;
        $this->accountRepo = $acoRepo;
        $this->contactRepo = $conRepo;
        $this->roleRepo = $rolRepo;
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
        $start_date_project = $this->projectRepo->find($args['project_id'])->start_date;
        $end_date_project = $this->projectRepo->find($args['project_id'])->end_date;
        $start_date_activity = $args['date_start'];
        $end_date_activity = $args['date_end'];
        if($start_date_activity >= $start_date_project || $args['is_added'] === True || $args['is_act'] === True)
        {
            if($end_date_project >= $end_date_activity || $args['is_added'] === True || $args['is_act'] === True)
            {
                $act = DB::transaction(function () use($args){
                    //verifica si la actividad es padre o hija para asi saber donde crear el folder
                    if ($args['parent_activity_id'] == null) {
                        //hace conexion con el drive y crea el folder. Metodos en Helper.php
                        $activity_folder = Conection_Drive()->files->create(Create_Folder($args['name'], $this->documentRepo->getFolderParentActivity($args['project_id'])->drive_id), ['fields' => 'id']);
                        $args['parent_document_id'] = $this->documentRepo->getFolderParentActivity($args['project_id'])->id;
                        $args['is_folder']          = 1; // 0 = Tipo File, 1 = Tipo Folder
                        $args['module_id']          = 1; //id 1 pertenece al modulo Activity
                        $args['drive_id']           = $activity_folder->id;
                        $activity = $this->activityRepo->create($args); //guarda registro de la nueva actividad
                        $args['activity_id']        = $activity->id;
                    }else {
                        //hace conexion con el drive y crea el folder. Metodos en Helper.php y
                        $activity_folder = Conection_Drive()->files->create(Create_Folder($args['name'], $this->documentRepo->getFolderSubActivity($args['project_id'], $args['parent_activity_id'])->drive_id), ['fields' => 'id']);
                        $args['parent_document_id'] = $this->documentRepo->getFolderSubActivity($args['project_id'], $args['parent_activity_id'])->id;
                        $args['is_folder']          = 1; // 0 = Tipo File, 1 = Tipo Folder
                        $args['module_id']          = 1; //id 1 pertenece al modulo Activity
                        $args['drive_id']           = $activity_folder->id;
                        $activity = $this->activityRepo->create($args); //guarda registro de la nueva actividad
                        $args['activity_id']        = $activity->id;
                    }
                    $doc_reference = $this->documentRepo->create($args); //guarda registro del nuevo documentReference

                    //Si es una acta registramo el movimeinto de el dinero que ingresa
                    if($args['is_act'] === True)
                    {
                        // Si no existe un integrante con rol de cliente en el proyecto no podemos registrar el acta, ni el moviento
                        if (!is_null($this->memberRepo->getClientMemberProject($this->roleRepo->getRolCliente()->id, $args['project_id'])))
                        {
                            $movement['puc_id'] = 40510;
                            $movement['project_id'] = $args['project_id'];
                            $movement['destination_id'] = $this->contactRepo->getContactIdentificatioNumber($args['project_id'])->id;
                            $movement['destination_role_id'] = $this->roleRepo->getRolProject()->id;
                            $movement['origin_id'] = $this->memberRepo->getClientMemberProject($this->roleRepo->getRolCliente()->id, $args['project_id'])->contact_id;
                            $movement['origin_role_id'] = $this->roleRepo->getRolCliente()->id;
                            $movement['movement_date'] = $args['date_end'];
                            if (empty($args['amount']) || is_null($args['amount'])) {
                                $movement['value'] = 0;
                            }else $movement['value'] = $args['amount'];
                            $movement['state_movement'] = True;
                            $movement['registration_date'] = now();
                            $movement['sender_id'] = auth()->user()->id;
                            $movement['activity_id'] = $activity->id;
                            if (empty($args['payment_method']) || is_null($args['payment_method'])) {
                                $movement['payment_method'] = null;
                            }else $movement['payment_method'] = $args['payment_method'];
                            $account_movement = $this->accountRepo->create($movement); // Creamos el moviento de ingreso de dinero del acta
                        }else {
                            return [
                                'activity' => null,
                                'message' => 'Asigne un Cliente a su proyecto, no podemos registrar este moviento en sus cuentas',
                                'type' => 'Failed'
                            ];
                        }
                    }

                    $this->Progress($args['is_act'], $args['project_id']); //actualizamos el porcentaje de progreso del proyecto
                    return [
                        'activity' => $activity,
                        'message' => 'Actividad creada',
                        'type' => 'Successful'
                    ];

                }, 3);
                //dd($act);
                return $act;
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

    public function Progress($is_act, $project_id){

        if ($is_act == true) { //Si es acta tenemos que actualizar el progreso del proyecto
            $acts_total = 0;
            $added_total = 0;
            $project = $this->projectRepo->find($project_id); //consultamos el proyecto
            $added_amount = $this->activityRepo->added_activity($project_id); //traemos todos los amount de los adicionales
            foreach ($added_amount as $add) {
                $added_total += $add->amount; //hacemos una sumatoria de los adicionales
            }
            $project_total = $added_total + $project->contract_value;
            $acts_amount = $this->activityRepo->act_activity($project_id); //consultamos amount de todas las actas recibidas en el proyecto

            foreach ($acts_amount as $amo) {
                $acts_total += $amo->amount; //Sumatoria de actas recibidas
            }
            $progress = round($acts_total/$project_total*100); //sacamos porcentaje de avance segun actas
            if ($progress <= 100) {
                $proj['progress'] = $progress;
                $update= $this->projectRepo->update($project_id, $proj); //Actualizamos el projecto con el nuevo porcentaje
            }else { //si el porcentaje da mas de 100% le asignamos 100
                $proj['progress'] = 100;
                $this->projectRepo->update($project_id, $proj);
            }
        }
    }
}


