<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Role;

class User extends Authenticatable
{
    protected $table = 'web_users';
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'steamid',
        'steam_nick',
        'steam_avatar',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool{
        return $this->hasRole('Admin');
    }

    public function roles(): BelongsToMany{
        return $this->belongsToMany(Role::class, 'web_role_user', 'user_id', 'role_id')->withTimestamps();
    }

    public function hasRole(string $roleName): bool{
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function assignRole(Role $role): void{
        if (!$this->hasRole($role->name)) {
            // asignar por relacion de id
            $this->roles()->attach($role->id);
            // Fallback: si falla el populate, actualizar timestamp de forma explicita
            // $this->roles()->newPivotStatement()
            //     ->where('user_id', $this->id)
            //     ->where('role_id', $role->id)
            //     ->update(['created_at' => now(), 'updated_at' => now()]);
        }
    }
}