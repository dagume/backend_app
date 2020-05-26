<?php
namespace App\Repositories;

use App\Accounting_movement;
use App\Repositories\BaseRepository;
use DB;

class Accounting_movementRepository extends BaseRepository
{
    public function getModel()
    {
        return new Accounting_movement;
    }

    public function getMovementAct($activity_id)
    {   //encuentra el movimiento relacionado con un acta
        $data = DB::select('select id from accounting_movements where activity_id = ?', [$activity_id]);
        return $data[0];
    }
    public function partner_contributions($project_id, $role_id)
    {   //reporte de aportes de socios al proyecto
        $data = DB::select('Select * from accounting_movements Where project_id = ? and origin_role_id = ?', [$project_id, $role_id]);
        return $data;
    }
}

