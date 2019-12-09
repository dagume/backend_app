<?php

namespace App\Repositories;

use App\Document_reference;
use App\Repositories\BaseRepository;
use DB;

class Document_referenceRepository extends BaseRepository
{
    public function getModel()
    {
        return new Document_reference;
    }
    public function getFolderContact()
    {
        //trae la referencia del folder raiz de Contactos
        $folderContact = DB::select('SELECT id, drive_id FROM document_reference WHERE name = ?', ['Contactos']);
        return $folderContact[0];
    }
    
}