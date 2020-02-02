<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document_member extends Model
{
    protected  $table= 'documents_member';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'member_id',
        'doc_id',
        'date',
        'file_id'
    ];

    public function member()
    {
        return $this->belongsTo('App\Member');
    }

    public function document_rol()
    {
        return $this->belongsTo('App\Document_rol', 'doc_id','id');
    }
}
