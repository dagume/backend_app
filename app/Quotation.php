<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected  $table= 'quotations';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'contact_id',
        'folder_id',
        'authorization',
        'date'
    ];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }
    public function contact()
    {
        return $this->belongsTo('App\User');
    }
}
