<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'user_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'order_created' => 'fa-plus-circle',
            'order_updated' => 'fa-edit',
            'status_changed' => 'fa-exchange-alt',
            'order_cancelled' => 'fa-times-circle',
            'product_added' => 'fa-shopping-cart',
            'product_removed' => 'fa-minus-circle',
            'note_added' => 'fa-sticky-note',
            'address_updated' => 'fa-map-marker-alt',
            default => 'fa-info-circle',
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'order_created' => 'success',
            'order_updated' => 'info',
            'status_changed' => 'primary',
            'order_cancelled' => 'danger',
            'product_added' => 'success',
            'product_removed' => 'warning',
            'note_added' => 'secondary',
            'address_updated' => 'info',
            default => 'secondary',
        };
    }

    public function getFormattedDescriptionAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        return match ($this->action) {
            'order_created' => 'Order was created',
            'order_updated' => 'Order was updated',
            'status_changed' => "Status changed from {$this->old_value} to {$this->new_value}",
            'order_cancelled' => 'Order was cancelled',
            'product_added' => 'Product was added to order',
            'product_removed' => 'Product was removed from order',
            'note_added' => 'Note was added',
            'address_updated' => 'Shipping address was updated',
            default => 'Activity recorded',
        };
    }
}
