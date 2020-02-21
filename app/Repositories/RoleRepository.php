<?php

namespace App\Repositories;

use Caffeinated\Shinobi\Models\Role;
use App\Repositories\BaseRepository;
use DB;

class RoleRepository extends BaseRepository
{
    public function getModel()
    {
        return new Role;
    }
    
}
