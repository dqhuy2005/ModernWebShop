<?php

namespace App\Models;

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

    /**
     * Get the user that owns the search history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for user searches
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for session searches
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId)->whereNull('user_id');
    }

    /**
     * Scope for recent searches
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('updated_at', 'desc')->limit($limit);
    }

    /**
     * Scope for popular searches
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('search_count', 'desc')->limit($limit);
    }

    /**
     * Scope to clean old records
     */
    public function scopeOlderThan($query, $days = 30)
    {
        return $query->where('updated_at', '<', now()->subDays($days));
    }
}
