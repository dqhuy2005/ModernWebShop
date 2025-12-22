<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;

class ProductView extends Model
{
    protected $fillable = [
        'product_id',
        'ip_address',
        'user_agent',
        'user_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    public function recent($query, int $days = 7)
    {
        return $query->where('viewed_at', '>=', now()->subDays($days));
    }

    #[Scope]
    public function forProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }
}
