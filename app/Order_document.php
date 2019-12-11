<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_document extends Model
{
    protected  $table= 'order_documents';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'document_id',
        'document_type',
        'code'        
    ];
    
    public function order()
    {
        return $this->belongsTo('App\Order');
    }
    
}
