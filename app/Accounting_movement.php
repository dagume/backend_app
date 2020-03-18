<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accounting_movement extends Model
{
    protected  $table = 'accounting_movements';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'puc_id', //
        'project_id', //
        'destination_id', //
        'destination_role_id', //
        'origin_id', //
        'origin_role_id', //
        'registration_date',
        'movement_date', //
        'payment_method', //
        'value', //
        'state_movement',
        'sender_id',
        'code', //
        //'exist_code'
    ];

    public function document_references()
    {
        return $this->hasMany('App\Document_reference', 'accounting_movements_id');
    }
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
        return $this->belongsTo('App\User', 'origin_id');
    }
    public function destination()
    {
        return $this->belongsTo('App\User', 'destination_id');
    }
}
