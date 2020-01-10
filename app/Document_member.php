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
        'date',
        'file_id'
    ];

    public function member()
    {
        return $this->belongsTo('App\Member');
    }
}
