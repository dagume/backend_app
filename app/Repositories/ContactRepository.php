<?php

namespace App\Repositories;

use App\User;
use App\Repositories\BaseRepository;
use DB;

class ContactRepository extends BaseRepository
{
    public function getModel()
    {
        return new User;
    }

    //public function lastContact()
    //{
    //    //Trae ultimo contacto registrado
    //    $reference = DB::select('SELECT id FROM contacts ORDER BY id DESC LIMIT 1'); //User::max('id')
    //    return $reference[0];
    //}


}
