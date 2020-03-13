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
    public function getRolProject()
    {
        $data = DB::select('select id from roles where name = ?', ['Proyecto']);
        return $data[0];
    }
    public function getRolProveedor()
    {
        $data = DB::select('select id from roles where name = ?', ['proveedor']);
        return $data[0];
    }
    

}
