<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const MIN_RATING = 1;
    public const MAX_RATING = 5;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'order_detail_id',
        'rating',
        'title',
        'comment',
        'images',
        'videos',
        'status',
        'admin_note',
        'is_verified_purchase',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'images' => 'array',
            'videos' => 'array',
            'is_verified_purchase' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function approve(): bool
    {
        return $this->update(['status' => self::STATUS_APPROVED]);
    }

    public function reject(?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'admin_note' => $reason,
        ]);
    }

    public function getStarsAttribute(): string
    {
        return str_repeat('⭐', $this->rating);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }
}
