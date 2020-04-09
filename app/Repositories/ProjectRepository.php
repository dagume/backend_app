<?php

namespace App\Repositories;

use App\Project;
use App\Repositories\BaseRepository;
use DB;

class ProjectRepository extends BaseRepository
{
    public function getModel()
    {
        return new Project;
    }

    public function payment_supports_project($project_id)
    {   //trae el folder_id de la carperta donde se guardaran soportes de movimientos
        $data = DB::select('select d_r.drive_id from projects as p inner join document_reference as d_r on  p.id = d_r.project_id
            where d_r.project_id = ? and d_r.is_folder = true and d_r.name = \'Contabilidad\'',[$project_id]);
        return $data[0];
    }
    public function get_contract_value($project_id)
    {   //trae el valor del contrato del proyecto sumando todos sus adicionales
        $data = DB::select('select amount from activities where project_id= ? and is_added = true and amount <> 0',[$project_id]);
        return $data;
    }
    public function get_projects_for_contact($contact_id)
    {   //trae el valor del contrato del proyecto sumando todos sus adicionales
        $data = DB::select('select amount from activities where project_id= ? and is_added = true and amount <> 0',[$project_id]);
        return $data;
    }
    public function get_filter_permission_project($name, $contact_id)
    {   //filtramos los projectos según este relacionados con usuario logueado y parametro enviado
        $data = DB::table('projects')
            ->select('projects.*')
            ->distinct()
            ->join('members', 'projects.id', '=', 'members.project_id')
            ->where('projects.name', 'ilike', $name)
            ->where('members.contact_id', $contact_id)
            ->whereNotNull('projects.parent_project_id')->get();
        return $data;
    }

}
