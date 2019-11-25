<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project_type extends Model
{
    protected  $table= 'project_type';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'name_project_type',
        'description_project_type',
    ];

    public function projects()
    {
        return $this->hasMany('App\Project');
    }
}
