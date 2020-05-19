<?php

namespace App\Repositories;

use App\Role;
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
        $data = DB::select('select id from roles where name = ?', ['Proveedor']);
        return $data[0];
    }
    public function getRolCliente()
    {
        $data = DB::select('select id from roles where name = ?', ['Cliente']);
        return $data[0];
    }


}
