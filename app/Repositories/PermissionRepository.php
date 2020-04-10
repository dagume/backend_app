<?php

namespace App\Repositories;

use Caffeinated\Shinobi\Models\Permission;
use App\Repositories\BaseRepository;
use DB;

class PermissionRepository extends BaseRepository
{
    public function getModel()
    {
        return new Permission;
    }
}
