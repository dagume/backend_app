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
        'order_id',
        'quantity',
        'value',
        'measure',
        'delivered_amount',
        'subtotal'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
    public function order()
    {
        return $this->belongsTo('App\Order');
    }
}
