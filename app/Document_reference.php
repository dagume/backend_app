<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document_reference extends Model
{
    protected  $table= 'document_reference';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'parent_document_id',
        'name',
        'is_folder',
        'activity_id',
        'project_id',
        'contact_id',
        'accounting_movements_id',
        'module_id',
        'order_document_id',
        'doc_id',
        'order_id',
        'drive_id'
    ];
    //falta relacion accounting_movements

    public function parent_document()
    {
        return $this->belongsTo('App\Document_reference');
    }
    public function document_references()
    {
        return $this->hasMany('App\Document_reference', 'parent_document_id');
    }
    public function module()
    {
        return $this->belongsTo('App\Module');
    }
    public function project()
    {
        return $this->belongsTo('App\Project');
    }
    public function activity()
    {
        return $this->belongsTo('App\Activity');
    }
    public function contact()
    {
        return $this->belongsTo('App\User' ,'contact_id');
    }
    public function document_contact()
    {
        return $this->belongsTo('App\Document_contact' ,'doc_id');
    }
}
