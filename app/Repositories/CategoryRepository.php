<?php

namespace App\Repositories;

use App\Category;
use App\Repositories\BaseRepository;
use DB;

class CategoryRepository extends BaseRepository
{
    public function getModel()
    {
        return new Category;
    }    
}