<?php

namespace Caffeinated\Shinobi\Models;

use App\Member;
use Illuminate\Database\Eloquent\Model;
use Caffeinated\Shinobi\Concerns\HasPermissions;
use Caffeinated\Shinobi\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model implements RoleContract
{
    use HasPermissions;

    /**
     * The attributes that are fillable via mass assignment.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'special'];

    /**
     * Create a new Role instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('shinobi.tables.roles'));
    }

    /**
     * Roles can belong to many users.
     *
     * @return Model
     */
    //public function users(): HasMany
    //{
    //    return $this->hasMany('App\User', 'id_role');
    //}
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    public function members(): hasMany
    {
        return $this->hasMany(Member::class);
    }
    //public function members(): BelongsToMany
    //{
    //    return $this->belongsToMany(config('auth.model') ?: config('auth.providers.members.model'))->withTimestamps();
    //}

    /**
     * Determine if role has permission flags.
     *
     * @return bool
     */
    public function hasPermissionFlags(): bool
    {
        return ! is_null($this->special);
    }

    /**
     * Determine if the requested permission is permitted or denied
     * through a special role flag.
     *
     * @return bool
     */
    public function hasPermissionThroughFlag(): bool
    {
        if ($this->hasPermissionFlags()) {
            return ! ($this->special === 'no-access');
        }

        return true;
    }
}
