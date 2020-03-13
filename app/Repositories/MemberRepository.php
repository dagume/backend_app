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
    public function project_idMember($member_id)
    {
        $data = DB::select('select project_id FROM members where id = ?',[$member_id]);
        return $data[0];
    }
    public function getMemberProject($project_id)
    {
        $data = DB::select('select contact_id from members where project_id = ? and state isnull', [$project_id]);
        return $data[0];
    }
    public function getNameMember($member_id)
    {
        //Traemos el identification number de ese contacto, el cual sera el id del proyecto al que pertenece
        $data = DB::select('select c.identification_number as contact_id from members as m
        inner join contacts as c on c.id = m.contact_id
        where m.id = ?', [$member_id]);
        return $data[0];
    }
    //PENDIENTE////////////////////////////////////
    //public function create_member(){
    //    $data = DB::select('select c.identification_number as contact_id from members as m
    //    inner join contacts as c on c.id = m.contact_id
    //    where m.id = ?', [$member_id]);
    //    return $this->find(3);
    //}
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
