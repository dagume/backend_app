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
    public function get_contacts_assets($type)
    {
        //Trae contactos segun tipo y que esten en estado activo
        $data = DB::table('contacts')
            ->select('contacts.*')
            ->where('state',1)
            ->where('type', $type);

        return $data;
    }
    public function get_contacts_assets_whitout_filter_type()
    {
        //Trae contactos de persona y empresa, que esten en estado activo
        $data = DB::table('contacts')
            ->select('contacts.*')
            ->where('state',1)
            ->where('type', '<>', 0);

        return $data;
    }
}
