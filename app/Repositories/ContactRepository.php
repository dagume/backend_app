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

    public function getFolderContact()
    {
        $folderContact = DB::select('SELECT id, drive_id FROM document_reference WHERE name = ?', ['Contactos']);
        return $folderContact[0];
    }
    public function refeFolder()
    {
        //User::max('id')
        $reference = DB::select('SELECT id FROM document_reference ORDER BY id DESC LIMIT 1');
        return $reference[0];
    }


}
