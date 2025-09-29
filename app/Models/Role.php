<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Permission;

class Role extends Model
{
    protected $table = 'web_roles';

    public function users()
    {
        return $this->belongsToMany(User::class, 'web_role_user', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'web_permission_role', 'role_id', 'permission_id');
    }
}