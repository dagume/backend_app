<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected  $table= 'module';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'name'
    ];

    public function document_references()
    {
        return $this->hasMany('App\Document_reference', 'parent_document_id');
    }
}
