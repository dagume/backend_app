<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected  $table= 'projects';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'project_type_id',
        'parent_project_id',
        'name',
        'start_date',
        'end_date',
        'description',
        'contract_value',
        'expenses',
        'progress',
        'state',
        'place',
        'address',
        'type',
        'association',
        'consortium_name',
        'consortium_nit',
        'folder_id',
    ];
    public function accounts(){
        return $this->hasMany('App\Account', 'project_id', 'id');
    }
    public function project_type()
    {
        return $this->belongsTo('App\Project_type');
    }
    public function parent_project()
    {
        return $this->belongsTo('App\Project');
    }
    public function members()
    {
        return $this->hasMany('App\Member');
    }
    public function activities()
    {
        return $this->hasMany('App\Activity');
    }
    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
