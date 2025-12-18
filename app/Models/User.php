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
    use SoftDeletes;

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

    public function isOAuthUser(): bool
    {
        return $this->oauthAccounts()->exists();
    }

    public function getOAuthProvider(): ?string
    {
        $oauthAccount = $this->oauthAccounts()->first();
        return $oauthAccount ? $oauthAccount->provider : null;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://'))) {
            return $this->image;
        }

        if ($this->image) {
            return asset($this->image);
        }

        return asset('images/default-avatar.png');
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
