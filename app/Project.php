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
        'process',
        'state',
        'place',
        'address',
        'type',
        'association',
        'consortium_name',
        'folder_id',
    ];
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
}
