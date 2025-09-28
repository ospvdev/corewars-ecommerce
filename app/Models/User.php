<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool{
        return $this->hasRole('admin');
    }

    public function roles(){
        return $this->belongsToMany(Role::class, 'web_role_user', 'user_id', 'role_id');
    }

    public function hasRole($roleName): bool{
        if (Auth::check()) {
            foreach (Auth::user()->roles as $role) {
                if ($role->name === $roleName) {
                    return true;
                }
            }
        }
        return false;
    }
}