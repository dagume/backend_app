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
    {   //trae la referencia del folder raiz de Contactos
        $folderContact = DB::select('SELECT id, drive_id FROM document_reference WHERE name = ?', ['Contactos']);
        return $folderContact[0];
    }
    public function getFolderAccounting($project_id)
    {   //trae la referencia del folder raiz de contabilidad
        $folderContact = DB::select('SELECT id, drive_id FROM document_reference WHERE name = ? and project_id = ? and is_folder is not null', ['Contabilidad',$project_id]);
        return $folderContact[0];
    }
    public function getFolderParentActivity($project_id)
    {   //Buscamos el folder padre donde se va crear el nuevo folder
        $folderActivity = DB::select('SELECT id, drive_id FROM document_reference WHERE project_id = ? AND name = ? and is_folder is not null', [$project_id , 'Actividades']);
        return $folderActivity[0];
    }
    public function getFolderSubActivity($project_id, $activity_id)
    {   //Buscamos el folder padre donde se va crear el nuevo folder
        $folderSubActivity = DB::select('SELECT id, drive_id FROM document_reference WHERE project_id = ? AND activity_id = ?', [$project_id, $activity_id]);
        return $folderSubActivity[0];
    }
    public function getFolderActYear()
    {   //Buscamos el folder del año actual
        $folderYear = DB::select('SELECT id, drive_id FROM document_reference WHERE name = ? ', [date("Y")]);
        return $folderYear[0];
    }
    public function getFolderOrders($project_id)
    {   //Buscamos el folder de ordenes dentro de un proyecto
        $folderOrder = DB::select('SELECT id, drive_id FROM document_reference WHERE project_id = ? AND name = ?', [$project_id , 'Ordenes']);
        return $folderOrder[0];
    }
    public function getFolderOrderCurrent($order_id)
    {   //Buscamos el folder de la orden actual
        $folderOrder = DB::select('SELECT * FROM document_reference WHERE order_id = ? ', [$order_id]);
        return $folderOrder[0];
    }
    public function getContactFolder($contact_id)
    {   //Buscamos el folder de un contacto en especifico
        $folderContact = DB::select('SELECT * FROM document_reference WHERE contact_id = ? ', [$contact_id]);
        return $folderContact[0];
    }
    public function getDocumentRequired($contact_id, $doc_id)
    {   //buscamos el registro de del documento requerido
        $folderContact = DB::select('select * from document_reference where contact_id = ? and doc_id = ?', [$contact_id, $doc_id]);
        if (empty($folderContact)) {
            return [];
        }
        return $folderContact[0];
    }
}
