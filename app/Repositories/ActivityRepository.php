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
    //public function getFolderContact()
    //{
    //    //trae la referencia del folder raiz de Contactos
    //    $folderContact = DB::select('SELECT id, drive_id FROM document_reference WHERE name = ?', ['Contactos']);
    //    return $folderContact[0];
    //}
    
    
}