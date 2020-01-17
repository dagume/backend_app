<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    protected  $table= 'measures';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    
}
