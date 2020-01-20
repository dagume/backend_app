<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_document extends Model
{
    protected  $table= 'order_documents';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'document_type',
        'code',   
        'date'     
    ];
    
    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function document_references()
    {
        return $this->hasMany('App\Document_reference', 'order_document_id');
    }
}
