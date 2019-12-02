<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected  $table= 'accounts';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'contact_id',
        'project_id',
        'parent_account_id',
        'name_account',
        'type_account'
    ];
    public function accounts(){
        return $this->hasMany('App\Account','parent_account_id', 'id');
    }
}
