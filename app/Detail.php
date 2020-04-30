<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected  $table= 'details';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'quo_id',
        'mea_id',
        'quantity',
        'value',
        'delivered_amount',
        'subtotal',
        'tax_id'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }
    public function quotation()
    {
        return $this->belongsTo('App\Quotation','quo_id');
    }
    public function measure()
    {
        return $this->belongsTo('App\Measure','mea_id','id');
    }
}
