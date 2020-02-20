<?php

namespace App\Repositories;

use App\Activity;
use App\Repositories\BaseRepository;
use DB;

class ActivityRepository extends BaseRepository
{
    public function getModel()
    {
        return new Activity;
    }
    public function todayActivity($date){
        //Traemos las actividades que vencen hoy
        $reference = DB::select('select * from activities where date_end = ?',[$date]);
        return $reference;
    }
    public function act_activity($project_id){
        //Traemos las actividades que son acta
        $acts = DB::select('select amount from activities where project_id = ? and is_act = true',[$project_id]);
        return $acts;
    }
    public function added_activity($project_id){
        //Traemos las actividades que son adicional
        $added = DB::select('select amount from activities where project_id = ? and is_added = true',[$project_id]);
        return $added;
    }
    //public function getFolderContact()
    //{
    //    //trae la referencia del folder raiz de Contactos
    //    $folderContact = DB::select('SELECT id, drive_id FROM document_reference WHERE name = ?', ['Contactos']);
    //    return $folderContact[0];
    //}
    
}