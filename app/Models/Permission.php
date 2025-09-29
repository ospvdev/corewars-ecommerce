<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'web_permissions';

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'web_permission_role', 'permission_id', 'role_id');
    }
}
