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
    public function getFolderParentActivity($project_id){
        //Buscamos el folder padre donde se va crear el nuevo folder
        $folderActivity = DB::select('SELECT id, drive_id FROM document_reference WHERE project_id = ? AND name = ?', [$project_id , 'Actividades']);
        return $folderActivity[0];
    }
    public function getFolderSubActivity($project_id, $parent_activity_id){
        //Buscamos el folder padre donde se va crear el nuevo folder
        $folderSubActivity = DB::select('SELECT id, drive_id FROM document_reference WHERE project_id = ? AND activity_id = ?', [$project_id, $parent_activity_id]);
        return $folderSubActivity[0];
    }
    
}