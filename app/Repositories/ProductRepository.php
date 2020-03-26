<?php

namespace App\Repositories;

use App\Product;
use App\Repositories\BaseRepository;
use DB;

class ProductRepository extends BaseRepository
{
    public function getModel()
    {
        return new Product;
    }
    public function getTransport()
    {
        //Trae el servicio de transporte, sea agregado por defecto en cada cotizacion que se realice
        $reference = DB::select('select * from products where name = \'Transporte\'');
        return $reference[0];
    }
}
