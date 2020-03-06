<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document_contact extends Model
{
    protected  $table= 'documents_contact';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'con_id',
        'doc_id'
    ];

    public function contact()
    {
        return $this->belongsTo('App\User','con_id');
    }

    public function document_rol()
    {
        return $this->belongsTo('App\Document_rol', 'doc_id','id');
    }
    public function document_references()
    {
        return $this->hasMany('App\Document_reference', 'doc_id','id');
    }
}
