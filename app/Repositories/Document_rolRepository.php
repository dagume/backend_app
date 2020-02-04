<?php

namespace App\Repositories;

use App\Document_rol;
use App\Repositories\BaseRepository;
use DB;

class Document_rolRepository extends BaseRepository
{
    public function getModel()
    {
        return new Document_rol;
    }
    public function docs_role($role_id)
    {        
        //traemos los documentos requeridos de cada role
        $req_doc = DB::select('SELECT * FROM documents_rol where role_id = ?',[$role_id]);
        return $req_doc;
    }
     
}