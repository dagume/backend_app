<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accounting_movement extends Model
{
    protected  $table= 'accounting_movements';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'puc_id', //
        'project_id', //
        'destination_id', //
        'origin_id', //
        'registration_date',
        'movement_date', //
        'payment_method', //
        'value', //
        'state_movement',
        'sender_id',
        'code', //
        'type_movement'
        //'exist_code'
    ];


    public function project()
    {
        return $this->belongsTo('App\Project');
    }
    public function puc()
    {
        return $this->belongsTo('App\Puc');
    }
    public function origin()
    {
        return $this->belongsTo('App\Member', 'origin_id');
    }
    public function destination()
    {
        return $this->belongsTo('App\Member', 'destination_id');
    }
}
