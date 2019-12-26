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

    public function lastProject()
    {
        //Trae ultimo proyecto registrado
        $reference = DB::select('SELECT id FROM projects ORDER BY id DESC LIMIT 1'); //Project::max('id')
        return $reference[0];
    }


}
