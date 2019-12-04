<?php

namespace App\Repositories;

use App\User;
use App\Repositories\BaseRepository;

class ContactRepository extends BaseRepository
{
    public function getModel()
    {
        return new User();
    }

}
