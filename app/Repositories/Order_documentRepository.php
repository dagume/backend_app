<?php

namespace App\Repositories;

use App\Order_document;
use App\Repositories\BaseRepository;
use DB;

class Order_documentRepository extends BaseRepository
{
    public function getModel()
    {
        return new Order_document;
    }
    public function getOrderDoc($order_id, $document_type)
    {
        //Buscamos el registro del order_doc
        $order_document = DB::select('SELECT * FROM order_documents WHERE order_id = ? AND document_type = ?', [$order_id, $document_type]);
        return $order_document[0];
    }
    public function getFolderOrder($order_id)
    {
        //Buscamos el drive_id del folder raiz de la orden
        $drive_id = DB::select('select drive_id from(select *  from document_reference where name in (select name from orders where id = ?) and order_id = ?) as name_order', [$order_id, $order_id]);
        return $drive_id[0];
    }
}
