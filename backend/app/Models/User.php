<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    use  SoftDeletes;

    protected $fillable = [
        'fullname',
        'email',
        'role_id',
        'phone',
        'password',
        'image',
        'status',
        'language',
        'birthday',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function oauthAccounts()
    {
        return $this->hasMany(OauthAccount::class);
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->isAdmin();
    }

    public function isUser(): bool
    {
        return $this->role && $this->role->isUser();
    }

    /**
     * Get user role name
     */
    public function getRoleName(): string
    {
        return $this->role ? $this->role->name : 'No Role';
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'fullname' => $this->fullname,
            'role' => $this->role ? $this->role->slug : null,
        ];
    }
}
