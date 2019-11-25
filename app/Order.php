<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected  $table= 'orders';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'quotation_id',
        'project_id',
        'contact_id',
        'name',
        'code',
        'application_date',
        'state',
        'description',
        'delivery_site',
        'sender_data',
        'subtotal',
        'total'
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
    public function details()
    {
        return $this->hasMany('App\Detail');
    }


}
