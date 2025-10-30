# 🔥 Product View Tracking & Auto Hot Status System

## 📖 Tổng Quan

Hệ thống tự động tracking views và cập nhật `is_hot` status cho sản phẩm dựa trên số lượt xem trong 7 ngày gần nhất.

### Business Rules
- ✅ **>= 100 views trong 7 ngày**: `is_hot = true`
- ❌ **< 50 views trong 7 ngày**: `is_hot = false`  
- ⏸️ **50-99 views**: Giữ nguyên status hiện tại
- 🛡️ **Anti-spam**: Chỉ tính 1 view/IP/user trong 2 phút

## 🏗️ Kiến Trúc

```
User Request
    ↓
Route: /products/{slug}
    ↓
Controller: ProductController@show
    ↓
Service: ProductViewService->trackView()
    ↓
Check Anti-Spam (Cache 2 min)
    ↓ (if valid)
Event: ProductViewed dispatched
    ↓
Queue: UpdateProductHotStatus Listener (async)
    ↓
1. Lưu ProductView record
2. Increment product.views
3. Tính recent views (7 days)
4. Update is_hot status
5. Clear cache
```

## 📦 Components

### 1. Database

**Table: `product_views`**
```sql
- id: bigint
- product_id: FK -> products.id
- ip_address: varchar(45)
- user_agent: varchar
- user_id: FK -> users.id (nullable)
- viewed_at: timestamp
- Indexes: [product_id, viewed_at], [product_id, ip_address, viewed_at]
```

**Table: `products`** (existing)
- `views`: integer (total views)
- `is_hot`: boolean

### 2. Models

- **ProductView**: Tracking model
- **Product**: Added `productViews()` relationship

### 3. Event & Listener

**Event: `ProductViewed`**
- Carries: product, IP, user_agent, user_id
- Queued: Yes (async processing)

**Listener: `UpdateProductHotStatus`**
- Implements `ShouldQueue`
- Retries: 3 times with backoff [10s, 30s, 60s]
- Actions:
  1. Save view tracking
  2. Increment views
  3. Calculate recent views
  4. Update is_hot status
  5. Clear cache

### 4. Service Layer

**ProductViewService**

Methods:
- `shouldCountView()`: Anti-spam check
- `trackView()`: Main tracking method
- `getRecentViewCount()`: Get 7-day views (cached)
- `getUniqueVisitorsCount()`: Unique visitors
- `getHotProducts()`: Get all hot products

### 5. Controller

**ProductController@show**
- Get product (cached 10 min)
- Track view via service
- Return view with stats

## 🚀 Setup Instructions

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Configure Queue

**.env**
```env
QUEUE_CONNECTION=database
# Or use redis for better performance
# QUEUE_CONNECTION=redis
```

**Run queue worker:**
```bash
php artisan queue:work --tries=3 --timeout=60
```

**Or use Supervisor for production:**
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --tries=3
autostart=true
autorestart=true
numprocs=3
```

### 3. Cache Configuration

**.env**
```env
CACHE_DRIVER=redis
# Or file/database
```

### 4. Register Service Provider (if needed)

Services are auto-injected via Laravel's service container.

## 📊 Usage Examples

### View Product Detail

```php
// Route automatically registered
GET /products/{slug}

// Controller handles:
- Product retrieval (cached)
- View tracking (anti-spam protected)
- Related products
- View statistics
```

### Get Hot Products

```php
use App\Services\ProductViewService;

$service = app(ProductViewService::class);
$hotProducts = $service->getHotProducts(10);
```

### Get Product Stats

```php
use App\Services\ProductViewService;

$service = app(ProductViewService::class);

// Recent views
$views7days = $service->getRecentViewCount($productId, 7);

// Unique visitors
$uniqueVisitors = $service->getUniqueVisitorsCount($productId, 7);
```

## 🔒 Anti-Spam Protection

### How it works:
1. User visits `/products/{slug}`
2. System generates cache key: `product_view_{productId}_ip_{hash}` or `product_view_{productId}_user_{userId}`
3. Check if key exists in cache
4. If exists → Skip tracking (spam detected)
5. If not → Track view + Set cache (2 minutes TTL)

### Benefits:
- ✅ No 429 errors
- ✅ Silent protection
- ✅ No UX interruption
- ✅ User can still view product

## ⚡ Performance Optimizations

### 1. Caching Strategy

```php
// Product detail: 10 minutes
Cache::remember("product_detail_{$slug}", now()->addMinutes(10));

// Recent views: 5 minutes
Cache::remember("product_{$id}_recent_views", now()->addMinutes(5));

// Related products: 1 hour
Cache::remember("related_products_{$id}", now()->addHour());

// Hot products: 1 hour
Cache::remember("hot_products", now()->addHour());
```

### 2. Database Indexes

```php
// Optimized for queries
$table->index(['product_id', 'viewed_at']);
$table->index(['product_id', 'ip_address', 'viewed_at']);
```

### 3. Async Processing

- Event dispatched immediately
- Processing happens in queue worker
- No delay in page response

## 🧪 Testing

### Test Anti-Spam

```php
use App\Services\ProductViewService;

$service = app(ProductViewService::class);

// First view - should count
$result1 = $service->shouldCountView(1, '127.0.0.1', null);
// true

// Second view within 2 minutes - should NOT count
$result2 = $service->shouldCountView(1, '127.0.0.1', null);
// false

// After 2 minutes - should count again
sleep(121);
$result3 = $service->shouldCountView(1, '127.0.0.1', null);
// true
```

### Test Hot Status Update

```php
use App\Models\Product;
use App\Models\ProductView;

$product = Product::find(1);

// Create 100 views in last 7 days
for ($i = 0; $i < 100; $i++) {
    ProductView::create([
        'product_id' => $product->id,
        'ip_address' => "192.168.1.{$i}",
        'viewed_at' => now()->subDays(rand(0, 6)),
    ]);
}

// Dispatch event
event(new ProductViewed($product, '127.0.0.1'));

// Process queue
Artisan::call('queue:work', ['--once' => true]);

// Check result
$product->refresh();
// $product->is_hot should be true
```

## 📈 Monitoring

### Check Queue Status

```bash
php artisan queue:failed
```

### View Logs

```php
// Logs are in storage/logs/laravel.log
Log::info('Product hot status updated', [
    'product_id' => $productId,
    'recent_views' => $recentViews,
    'is_hot' => $isHot,
]);
```

### Monitor Cache

```php
use Illuminate\Support\Facades\Cache;

// Check if anti-spam is working
$hasView = Cache::has("product_view_1_ip_" . md5('127.0.0.1'));
```

## 🛠️ Maintenance

### Clean Old Views (Optional)

Create a scheduled command to clean views older than 30 days:

```php
// app/Console/Commands/CleanOldProductViews.php
public function handle()
{
    $deletedCount = ProductView::where('viewed_at', '<', now()->subDays(30))
        ->delete();
    
    $this->info("Deleted {$deletedCount} old product views");
}

// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('products:clean-old-views')
        ->daily()
        ->at('02:00');
}
```

## 🎯 Best Practices

1. **Always use Queue Worker in production**
2. **Use Redis for cache** (better than file)
3. **Monitor queue failures**
4. **Set up proper logging**
5. **Regular database maintenance**
6. **Monitor cache hit rates**

## ⚠️ Troubleshooting

### Views not counting?
- Check queue worker is running: `ps aux | grep queue:work`
- Check failed jobs: `php artisan queue:failed`
- Check cache driver is working

### is_hot not updating?
- Check listener is registered (auto-discovered in Laravel 11)
- Check queue worker is processing
- Check logs for errors

### Performance issues?
- Enable query caching
- Use Redis instead of database cache
- Add more queue workers
- Optimize database indexes

## 📝 License

Internal project documentation.
