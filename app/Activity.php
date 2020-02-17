<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected  $table= 'activities';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'parent_activity_id',
        'name',
        'description',
        'date_start',
        'date_end',
        'state',
        'completed',
        'priority',
        'notes',
        'amount',
        'is_added',
        'drive_id',
        'is_act'
    ];

    //public function project()
    //{
    //    return $this->belongsTo('App\Project');
    //}
    public function activities()
    {
        return $this->hasMany('App\Activity', 'parent_activity_id');
    }


}
