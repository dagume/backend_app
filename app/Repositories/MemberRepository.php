<?php

namespace App\Repositories;

use App\Member;
use App\Repositories\BaseRepository;
use DB;

class MemberRepository extends BaseRepository
{
    public function getModel()
    {
        return new Member;
    }
    public function roles_contact($contact_id, $project_id)
    {        
        //traemos todos los roles de un contacto dentro de un projecto
        $roles = DB::select('SELECT role_id FROM members where contact_id = ? and project_id = ?',[$contact_id, $project_id]); //Activity::max('id')
        //foreach ($roles as $rol) {
        //    $role_id[] = $rol->role_id;
        //}
        return $roles;
    }
}