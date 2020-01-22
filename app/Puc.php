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
}
