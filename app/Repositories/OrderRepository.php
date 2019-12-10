<?php

namespace App\Repositories;

use App\Order;
use App\Repositories\BaseRepository;
use DB;

class OrderRepository extends BaseRepository
{
    public function getModel()
    {
        return new Order;
    }
    public function lastOrder()
    {        
        //Trae la ultima orden registrada
        $reference = DB::select('SELECT id FROM orders ORDER BY id DESC LIMIT 1'); //Order::max('id')
        return $reference[0];
    }
     
    //public function getFolderContact()
    //{
    //    //trae la referencia del folder raiz de Contactos
    //    $folderContact = DB::select('SELECT id, drive_id FROM document_reference WHERE name = ?', ['Contactos']);
    //    return $folderContact[0];
    //}
    
    
}