<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Taxe extends Model
{
    protected $table = 'taxes';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'percentage'
    ];

    public function products()
    {
        return $this->hasMany('App\Product', 'tax_id');
    }
}
