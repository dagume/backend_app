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

    public function deleteContact_Project($project_id, $contact_id)
    {
        $data = DB::select('DELETE FROM members where project_id = ? and contact_id = ?',[$project_id, $contact_id]);
        return $data;
    }
    //public function mem_rol_contact($contact_id)
    //{
    //    //traemos todos los roles de un contacto en todo el sistema
    //    $roles = DB::select('SELECT id, role_id FROM members where contact_id = ?',[$contact_id]);
    //    //foreach ($roles as $rol) {
    //    //    $role_id[] = $rol->role_id;
    //    //}
    //    return $roles;
    //}
}
