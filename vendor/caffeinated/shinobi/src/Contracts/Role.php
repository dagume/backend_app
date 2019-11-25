<?php

namespace Caffeinated\Shinobi\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface Role
{
    /**
     * Roles can belong to many users.
     *
     * @return Model
     */
    public function users(): BelongsToMany;
    public function members(): hasMany;

    public function hasPermissionFlags(): bool;
    public function hasPermissionThroughFlag(): bool;
}
