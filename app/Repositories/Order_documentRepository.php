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
}