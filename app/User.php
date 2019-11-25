<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Caffeinated\Shinobi\Concerns\HasRolesAndPermissions;
use Caffeinated\Shinobi\Models\Role;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRolesAndPermissions;

    protected  $table= 'contacts';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'parent_contact_id',
        'type',
        'name',
        'lastname',
        'identification_type',
        'identification_number',
        'email',
        'phones',
        'state',
        'locate',
        'city',
        'address',
        'web_site',
        'password',
        'folder_id',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    //Falta Relacion con cuentas
    public function contact()
    {
        return $this->BelongsTo(User::class, 'parent_contact_id');
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'members', 'contact_id', 'role_id')->withTimestamps();
    }
    public function members()
    {
        return $this->hasMany('App\Member', 'contact_id', 'id');
    }
    public function orders()
    {
        return $this->hasMany('App\Order', 'contact_id', 'id');
    }
    public function quotations()
    {
        return $this->hasMany('App\Quotation', 'contact_id', 'id');
    }
}
