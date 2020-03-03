<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected  $table= 'orders';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'contact_id',
        'name',
        'application_date',
        'state',
        'description',
        'delivery_site',
        'subtotal',
        'total',
        'sender_data',
        'lat',
        'lon'
    ];
    public function quotations()
    {
        return $this->hasMany('App\Quotation');
    }
    public function project()
    {
        return $this->belongsTo('App\Project');
    }
    public function contact()
    {
        return $this->belongsTo('App\User');
    }
    public function order_documents()
    {
        return $this->hasMany('App\Order_document');
    }
    public function payment_agreements()
    {
        return $this->hasMany('App\PaymentAgreement');
    }
}
