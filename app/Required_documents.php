<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;

class Required_documents extends Model
{
    protected  $table= 'required_documents';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'name_required_documents'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'documents_rol','role_id', 'required_document_id');
    }
}
