<?php

namespace App\Repositories;

use App\Puc;
use App\Repositories\BaseRepository;
use DB;

class PucRepository extends BaseRepository
{
    public function getModel()
    {
        return new Puc;
    }
}
