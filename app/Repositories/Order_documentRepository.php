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
}