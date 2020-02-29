<?php

namespace App\Repositories;

use App\Taxe;
use App\Repositories\BaseRepository;
use DB;

class TaxeRepository extends BaseRepository
{
    public function getModel()
    {
        return new Taxe;
    }
}
