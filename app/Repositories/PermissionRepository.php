<?php

namespace App\Repositories;

use App\Permission;
use App\Repositories\BaseRepository;
use DB;

class PermissionRepository extends BaseRepository
{
    public function getModel()
    {
        return new Permission;
    }
}
