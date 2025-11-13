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
        'slug',
        'specifications',
        'description',
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

    public function getImageUrlAttribute(): string
    {
        $mainImage = $this->main_image;
        if ($mainImage) {
            return asset('storage/' . $mainImage);
        }
        return asset('assets/imgs/products/default.png');
    }

    public function getUrlAttribute(): string
    {
        return route('products.show', $this->slug);
    }

    public function toSearchSuggestion(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'url' => $this->url,
        ];
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

    public function productViews()
    {
        return $this->hasMany(ProductView::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->approved()->with('user');
    }

    public function getMainImageAttribute()
    {
        if ($this->relationLoaded('images')) {
            $first = $this->images->first();
            if ($first) {
                return $first->path;
            }
        } else {
            $first = $this->images()->first();
            if ($first) {
                return $first->path;
            }
        }

        return null;
    }

    public function getRecentViewsCount(int $days = 7): int
    {
        return $this->productViews()
            ->where('viewed_at', '>=', now()->subDays($days))
            ->count();
    }

    /**
     * Get average rating from approved reviews
     */
    public function getAverageRating(): float
    {
        return (float) $this->approvedReviews()->avg('rating') ?: 0;
    }

    /**
     * Get total count of approved reviews
     */
    public function getReviewsCount(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get rating breakdown (count per star)
     */
    public function getRatingBreakdown(): array
    {
        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $breakdown[$i] = $this->approvedReviews()->where('rating', $i)->count();
        }
        return $breakdown;
    }
}
