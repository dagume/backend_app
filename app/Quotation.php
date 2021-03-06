<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    public function __construct()
    {}
    protected  $table= 'quotations';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'contact_id',
        'file_id',
        'authorized',
        'date',
        'hash_id',
        'file_date',
        'discount',
        'discount_type',
        'received'
    ];

    public function details()
    {
        return $this->hasMany('App\Detail','quo_id');
    }
    public function order()
    {
        return $this->belongsTo('App\Order');
    }
    public function contact()
    {
        return $this->belongsTo('App\User');
    }
}
