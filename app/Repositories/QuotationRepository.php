<?php

namespace App\Repositories;

use App\Quotation;
use App\Repositories\BaseRepository;
use DB;

class QuotationRepository extends BaseRepository
{
    public function getModel()
    {
        return new Quotation;
    }
    //public function lastQuotation()
    //{
    //    //Trae la ultima cotizacion registrada
    //    $reference = DB::select('SELECT id FROM quotations ORDER BY id DESC LIMIT 1'); //Quotation::max('id')
    //    return $reference[0];
    //}
    public function updateQuotation($quotation_id, $quotation_hash)
    {
        //actualiza una cotizacion
        $reference = DB::select('UPDATE quotations SET  hash_id = ? WHERE id = ?', [$quotation_hash, $quotation_id]);
    }
    public function QuotationsForOrder($order_id)
    {
        //Trae las cotizaciones de una orden
        $reference = DB::select('SELECT * FROM quotations where order_id = ?',[$order_id]);
        return $reference;
    }

    public function getQuotation($order_id, $contact_id)
    {
        //Trae una cotizacion en especifico
        $reference = DB::select('SELECT * FROM quotations where order_id = ? AND contact_id = ?', [$order_id, $contact_id]);
        return $reference[0];
    }

    public function getAssociation($quotation_id)
    {
        //Trae una cotizacion en especifico
        $data = DB::select('select p.association from quotations as q
        inner join orders as o on q.order_id = o.id
        inner join projects as p on p.id = o.project_id
        where  q.id = ?', [$quotation_id]);
        return $data[0];
    }

    //public function addQuotation($order_id, $contact_id)
    //{
    //    //actualiza una cotizacion
    //    $reference = DB::select('INSERT INTO quotations(order_id, contact_id ) VALUES (?, ?)', [$order_id, $contact_id]);
    //    return $reference[0];
    //}
}
