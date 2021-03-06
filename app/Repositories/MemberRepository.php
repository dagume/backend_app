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
    public function getClientMemberProject($role_id, $project_id)
    {
        //Traemos el cliente asignado al proyecto
        $data = DB::select('select contact_id from members where role_id = ? and project_id = ?', [$role_id, $project_id]);
        if (empty($data)) {
            return null;
        }
        return $data[0];
    }
    //PENDIENTE////////////////////////////////////
    //public function create_member()
    //{
    //    $data = DB::select('select c.identification_number as contact_id from members as m
    //    inner join contacts as c on c.id = m.contact_id
    //    where m.id = ?', [$member_id]);
    //    return $this->find(3);
    //}
    public function get_member($project_id, $contact_id, $role_id)
    {
        //Consultamos si ese miembro ya existe en base de datos
        $data = DB::select('select * from members where project_id = ? and contact_id = ? and role_id = ?',[$project_id, $contact_id, $role_id]);
        //dd(empty($data[0]));
        return $data;
    }
    public function get_user_has_project($contact_id, $project_id)
    {
        //Consultamos si ese contacto pertenece a un proyecto en especifico
        $data = DB::select('select * from members where contact_id = ? and project_id = ?',[$contact_id, $project_id]);
        //dd(empty($data[0]));
        return $data;
    }
    public function get_members_for_a_contact($contact_id)
    {
        //Consultamos si ese contacto es integrante de algun proyecto
        $data = DB::select('select * from members where contact_id = ?',[$contact_id]);
        //dd(empty($data[0]));
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
