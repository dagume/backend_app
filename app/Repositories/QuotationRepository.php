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
    $reference = DB::select('SELECT * FROM quotations where order_id = ?',[$order_id]); //Quotation::max('id')
    return $reference;
} 
    
    //public function addQuotation($order_id, $contact_id)
    //{        
    //    //actualiza una cotizacion
    //    $reference = DB::select('INSERT INTO quotations(order_id, contact_id ) VALUES (?, ?)', [$order_id, $contact_id]);        
    //    return $reference[0];
    //} 
}