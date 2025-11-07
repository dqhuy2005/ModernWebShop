<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'notification_type_id',
        'email_template_id',
        'user_id',
        'recipient_email',
        'recipient_name',
        'recipient_phone',
        'related_type',
        'related_id',
        'channel',
        'status',
        'subject',
        'content',
        'template_data',
        'retry_count',
        'max_retry',
        'scheduled_at',
        'sent_at',
        'read_at',
        'clicked_at',
        'failed_at',
        'error_message',
        'error_trace',
        'email_service',
        'message_id',
        'notes',
    ];

    protected $casts = [
        'template_data' => 'array',
        'error_trace' => 'array',
        'retry_count' => 'integer',
        'max_retry' => 'integer',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'clicked_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // Relationships
    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }

    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relatedModel()
    {
        return $this->morphTo('related');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeRetryable($query)
    {
        return $query->where('status', 'failed')
            ->whereRaw('retry_count < max_retry');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'pending')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now());
    }

    // Helper methods
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed($errorMessage, $errorTrace = null)
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $errorMessage,
            'error_trace' => $errorTrace,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    public function markAsClicked()
    {
        $this->update([
            'status' => 'clicked',
            'clicked_at' => now(),
        ]);
    }

    public function canRetry()
    {
        return $this->status === 'failed' && $this->retry_count < $this->max_retry;
    }

    public function incrementRetry()
    {
        $this->increment('retry_count');
    }
}
