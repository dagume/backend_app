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
}