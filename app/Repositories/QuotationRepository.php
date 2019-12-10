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
    public function lastQuotation()
    {
        //Trae la ultima cotizacion registrada
        $reference = DB::select('SELECT id FROM quotations ORDER BY id DESC LIMIT 1'); //Quotation::max('id')
        return $reference[0];
    } 
}