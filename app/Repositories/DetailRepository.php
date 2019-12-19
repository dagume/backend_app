<?php

namespace App\Repositories;

use App\Detail;
use App\Repositories\BaseRepository;

class DetailRepository extends BaseRepository
{
    public function getModel()
    {
        return new Detail;
    }
    
}