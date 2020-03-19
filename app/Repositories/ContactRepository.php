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

    public function getContactIdentificatioNumber($identification_number)
    {
        //Trae contacto segun numero de identificacion
        $reference = DB::select('SELECT id FROM contacts where identification_number = ?',[$identification_number]);
        return $reference[0];
    }


}
