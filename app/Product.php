<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected  $table= 'products';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'tax_id'
    ];

    public function taxe()
    {
        return $this->belongsTo('App\Taxe');
    }
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

}

