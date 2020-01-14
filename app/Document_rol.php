<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document_rol extends Model
{
    protected  $table= 'documents_rol';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'required_document_id'
    ];

    public function require_document()
    {
        return $this->belongsTo('App\Required_documents', 'required_document_id','id');
    }
    public function documents_member()
    {
        return $this->hasMany('App\Document_member','doc_id','id');
    }
}
