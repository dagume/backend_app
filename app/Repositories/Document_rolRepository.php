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
    //public function docs_role($role_id)
    //{        
    //    //traemos los documentos requeridos de cada role
    //    $req_doc = DB::select('SELECT * FROM documents_rol where role_id IN ('.$role_id.')');
    //    return $req_doc;
    //}
    public function getDocUpload($doc_id)
    {        
        //traemos la data del documento que se esta subiendo
        $req_doc = DB::select(' select * from documents_rol as doc_rol
                                    inner join required_documents as req_doc
                                    on doc_rol.required_document_id = req_doc.id where doc_rol.id = ?',[$doc_id]);
        return $req_doc[0];
    }
}