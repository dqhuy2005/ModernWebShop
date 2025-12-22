<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    const ADMIN = 'admin';
    const USER = 'user';

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function isAdmin(): bool
    {
        return $this->slug === self::ADMIN;
    }

    public function isUser(): bool
    {
        return $this->slug === self::USER;
    }
}
