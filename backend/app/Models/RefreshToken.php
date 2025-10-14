<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'ip_address',
        'user_agent',
        'is_revoked',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'expires_at' => 'datetime',
            'is_revoked' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid()
    {
        return !$this->is_revoked && $this->expires_at->isFuture();
    }

    public function revoke()
    {
        $this->is_revoked = true;
        return $this->save();
    }

    public function scopeValid($query)
    {
        return $query->where('is_revoked', false)
            ->where('expires_at', '>', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }
}
