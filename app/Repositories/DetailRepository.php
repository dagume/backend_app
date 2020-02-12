<?php

namespace App\Repositories;

use App\Detail;
use App\Repositories\BaseRepository;
use DB;

class DetailRepository extends BaseRepository
{
    public function getModel()
    {
        return new Detail;
    }
    public function getDataPDF($order_id)
    {
        //Buscamos el folder padre donde se va crear el nuevo folder
        $detailsPDF = DB::select('select p.id as product_id, p.name as product_name, m.name as measure_name, d.quantity from orders as o
        inner join details as d on o.id = d.order_id
        inner join products as p on d.product_id = p.id
        inner join measures as m on d.mea_id = m.id
        where  d.order_id = ?', [$order_id]);
        return $detailsPDF;
    }
    //public function getDataPdfOrder($order_id)
    //{
    //    //Buscamos el folder padre donde se va crear el nuevo folder
    //    $detailsPDF = DB::select('select p.id as product_id, p.name as product_name, m.name as measure_name, d.quantity from orders as o
    //    inner join details as d on o.id = d.order_id
    //    inner join products as p on d.product_id = p.id
    //    inner join measures as m on d.mea_id = m.id
    //    where  d.order_id = ?', [$order_id]);
    //    return $detailsPDF;
    //}
}
