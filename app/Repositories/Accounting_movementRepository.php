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
}

