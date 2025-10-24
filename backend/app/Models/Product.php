<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'specifications',
        'description',
        'image',
        'price',
        'currency',
        'status',
        'parent_id',
        'language',
        'views',
        'is_hot',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'specifications' => 'array',
            'price' => 'integer',
            'status' => 'boolean',
            'parent_id' => 'integer',
            'views' => 'integer',
            'is_hot' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeHot($query)
    {
        return $query->where('is_hot', true);
    }

    public function scopeMostViewed($query, $limit = 10)
    {
        return $query->orderBy('views', 'desc')->limit($limit);
    }

    public function scopeSearch($query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where('name', 'LIKE', '%' . $keyword . '%');
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price === 0 || $this->price === null) {
            return 'Liên hệ';
        }
        return number_format($this->price, 0, ',', '.') . ' ₫';
    }

    public function getFormattedPriceWithCurrencyAttribute(): string
    {
        if ($this->price === 0 || $this->price === null) {
            return 'Liên hệ';
        }
        return number_format($this->price, 0, ',', '.') . ' ' . strtoupper($this->currency ?? 'VND');
    }

    // Relationships

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
