<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'keyword',
        'search_count',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'search_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    public function forUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId)->whereNull('user_id');
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('updated_at', 'desc')->limit($limit);
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('search_count', 'desc')->limit($limit);
    }

    public function scopeOlderThan($query, $days = 30)
    {
        return $query->where('updated_at', '<', now()->subDays($days));
    }
}
