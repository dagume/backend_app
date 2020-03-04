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
    public function getIDContactsOrder($order_id)
    {
        //Trae el id de los contactos a los que se ha cotizado en la orden
        $data = DB::select('select contact_id from quotations where order_id = ?', [$order_id]);
        //dd($data);
        foreach ($data as $dat) {
            $ids[] = $dat->contact_id; 
        }
        return $ids;
    }
}
