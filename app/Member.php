<?php

namespace App;
use Caffeinated\Shinobi\Models\Role;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected  $table= 'members';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'contact_id',
        'role_id',
        'hours_week',
        'hours_month',
        'state'
    ];


    public function project()
    {
        return $this->belongsTo('App\Project');
    }
    public function contact()
    {
        return $this->belongsTo('App\User');
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}
