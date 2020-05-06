<?php

namespace App\Repositories;

use App\Required_documents;
use App\Repositories\BaseRepository;
use DB;

class RequiredDocumentRepository extends BaseRepository
{
    public function getModel()
    {
        return new Required_documents;
    }
}
