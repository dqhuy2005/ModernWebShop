<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'is_active',
        'email_enabled',
        'sms_enabled',
        'push_enabled',
        'default_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'default_config' => 'array',
    ];

    // Relationships
    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function activeEmailTemplate($locale = 'vi')
    {
        return $this->hasOne(EmailTemplate::class)
            ->where('is_active', true)
            ->where('is_default', true)
            ->where('locale', $locale);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeEmailEnabled($query)
    {
        return $query->where('email_enabled', true);
    }

    // Helper methods
    public static function findByCode($code)
    {
        return static::where('code', $code)->first();
    }

    public function getDefaultRetryCount()
    {
        return $this->default_config['retry_count'] ?? 3;
    }

    public function getDefaultDelay()
    {
        return $this->default_config['delay_seconds'] ?? 60;
    }
}
