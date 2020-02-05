<?php

namespace App\Repositories;

use App\Document_member;
use App\Repositories\BaseRepository;
use DB;

class Document_memberRepository extends BaseRepository
{
    public function getModel()
    {
        return new Document_member;
    }    
}