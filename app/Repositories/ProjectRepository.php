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



}
