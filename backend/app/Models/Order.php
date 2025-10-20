<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_amount',
        'total_items',
        'status',
        'address',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'total_amount' => 'integer',
            'total_items' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPING = 'shipping';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function activities()
    {
        return $this->hasMany(OrderActivity::class)->latest();
    }

    public function logActivity(
        string $action,
        ?string $description = null,
        ?string $oldValue = null,
        ?string $newValue = null
    ): OrderActivity {
        return $this->activities()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Helper Methods - Status Badges

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_PROCESSING => 'primary',
            self::STATUS_SHIPPING => 'secondary',
            self::STATUS_SHIPPED => 'secondary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_REFUNDED => 'dark',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'PENDING',
            self::STATUS_CONFIRMED => 'CONFIRMED',
            self::STATUS_PROCESSING => 'PROCESSING',
            self::STATUS_SHIPPING => 'SHIPPING',
            self::STATUS_SHIPPED => 'SHIPPED',
            self::STATUS_COMPLETED => 'COMPLETED',
            self::STATUS_DELIVERED => 'DELIIVERED',
            self::STATUS_CANCELLED => 'CANCELLED',
            self::STATUS_REFUNDED => 'REFUNDED',
            default => ucfirst($this->status),
        };
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', '.') . ' ₫';
    }

    public function calculateTotalAmount(): int
    {
        return $this->orderDetails->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });
    }

    public function calculateTotalItems(): int
    {
        return $this->orderDetails->sum('quantity');
    }

    public function verifyIntegrity(): bool
    {
        $calculatedTotal = $this->calculateTotalAmount();
        return $this->total_amount === $calculatedTotal;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isShipping()
    {
        return $this->status === self::STATUS_SHIPPING;
    }

    public function isShipped()
    {
        return $this->status === self::STATUS_SHIPPED;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isDelivered()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isRefunded()
    {
        return $this->status === self::STATUS_REFUNDED;
    }
}
