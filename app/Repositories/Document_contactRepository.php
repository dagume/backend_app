<?php

namespace App\Repositories;

use App\Document_contact;
use App\Repositories\BaseRepository;
use DB;

class Document_contactRepository extends BaseRepository
{
    public function getModel()
    {
        return new Document_contact;
    }    
}