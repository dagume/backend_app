<?php

namespace App\Repositories;

use App\Measure;
use App\Repositories\BaseRepository;
use DB;

class MeasureRepository extends BaseRepository
{
    public function getModel()
    {
        return new Measure;
    }    
}