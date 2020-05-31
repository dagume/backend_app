<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Puc extends Model
{
    public function __construct()
    {}
    protected  $table= 'puc';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'parent_puc_id',
        'name',
        'description'
    ];

    public function son_pucs()
    {
        return $this->hasMany('App\Puc','parent_puc_id');
    }
    public function parent_pucs()
    {
        return $this->belongsTo('App\Puc','parent_puc_id');
    }
    public function accounting_movements()
    {
        return $this->hasMany('App\Accounting_movement','puc_id');
    }
}
