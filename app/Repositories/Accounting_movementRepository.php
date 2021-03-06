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
        return $this->getObjects($data);
    }
    public function a_partner_contributions($project_id, $origin_id, $role_id)
    {   //reporte de aportes de un socio al proyecto
        $data = DB::select('select * from accounting_movements where project_id = ? and origin_id = ? and origin_role_id = ?', [$project_id, $origin_id, $role_id]);
        return $this->getObjects($data);
    }
    public function partner_contributions_and_refunds($project_id, $contact_id, $role_id, $projectContact_id)
    {   //Aportes y reembolsos de socio o prestamista en un proyecto
        //Le estamos parando los roles de origen y destino por defecto ya que a ultima hora se cambio el fucionamieto de que cuando un proyecto se vuelve miembro de otro proyecto, llega con el rol de prestamista(que es el rol 7)
        $data = DB::select('select * from accounting_movements
        where project_id = ?
        and (origin_id = ? and destination_id = ? and origin_role_id in(?,9))
        or (project_id = ? and origin_id = ? and destination_id = ? and destination_role_id in(?,9))',
        [$project_id, $contact_id, $projectContact_id, $role_id, $project_id, $projectContact_id, $contact_id, $role_id]);
        return $this->getObjects($data);
    }
    public function project_income_refunds($project_id)
    {   //reporte de Registro de ingresos y egresos en general del proyecto
        $data = DB::select('select * from accounting_movements where project_id = ?', [$project_id]);
        return $this->getObjects($data);
    }
    public function project_expenses($project_id, $origin_id, $role_id)
    {   //reporte de Registro de egresos en general del proyecto
        $data = DB::select('select * from accounting_movements where project_id = ? and origin_id = ? and origin_role_id = ?', [$project_id, $origin_id, $role_id]);
        return $this->getObjects($data);
    }
    public function project_expenses_for_concept($project_id, $origin_id, $role_id, $puc_id)
    {   //reporte de Registro de egresos por concepto del proyecto
        $data = DB::select('select * from accounting_movements where project_id = ? and origin_id = ? and origin_role_id = ? and puc_id = ?', [$project_id, $origin_id, $role_id, $puc_id]);
        return $this->getObjects($data);
    }
    public function project_income($project_id, $destination_id, $role_id)
    {   //reporte de Registro de ingresos del proyecto
        $data = DB::select('select * from accounting_movements where project_id = ? and destination_id = ? and destination_role_id = ?', [$project_id, $destination_id, $role_id]);
        return $this->getObjects($data);
    }
    public function project_expense_cost_report($project_id, $origin_id, $role_id, $puc_id)
    {   //reporte de Registro de egresos o costos en general del proyecto
        $data = DB::select('select * from accounting_movements
		where project_id = ?
		and origin_id = ?
		and origin_role_id = ?
		and CAST(puc_id AS CHAR) LIKE \''.$puc_id.'\';', [$project_id, $origin_id, $role_id]);
        return $this->getObjects($data);
    }
    public function movements_for_member($member_id)
    {   //movimientos por miembro
        $data = DB::select('select * from accounting_movements where origin_id = ? or destination_id = ?;', [$member_id, $member_id]);
        return empty($data);
    }
}

