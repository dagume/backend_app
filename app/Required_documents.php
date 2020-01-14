<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Caffeinated\Shinobi\Models\Role;

class Required_documents extends Model
{
    protected  $table= 'required_documents';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'name_required_documents'
    ];

    public function documents_rol(){
        return $this->hasMany('App\Document_rol', 'required_document_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'documents_rol','role_id', 'required_document_id');
    }
}
