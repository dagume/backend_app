<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentAgreement extends Model
{
    protected  $table= 'payment_agreement';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'pay_date',
        'amount',
        'state'
        //'next_pay'
    ];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }
}
