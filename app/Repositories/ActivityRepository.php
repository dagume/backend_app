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

    //public function getFolderContact()
    //{
    //    //trae la referencia del folder raiz de Contactos
    //    $folderContact = DB::select('SELECT id, drive_id FROM document_reference WHERE name = ?', ['Contactos']);
    //    return $folderContact[0];
    //}
    //public function lastContact()
    //{        
    //    //Trae ultimo contacto registrado
    //    $reference = DB::select('SELECT id FROM contacts ORDER BY id DESC LIMIT 1'); //User::max('id')
    //    return $reference[0];
    //}
    
}