# 🛒 ModernWebShop - High-Performance E-Commerce Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red.svg" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP">
  <img src="https://img.shields.io/badge/Redis-Caching-dc382d.svg" alt="Redis">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
  <img src="https://img.shields.io/badge/Status-Production Ready-success.svg" alt="Status">
</p>

## 📖 About The Project

**ModernWebShop** is a production-ready e-commerce platform built with **Laravel 12**, featuring advanced caching strategies, real-time cart management, and seamless AJAX-driven user experience. The project emphasizes performance optimization, clean architecture, and scalability.

### 🎯 Key Highlights
- **🚀 High Performance:** Redis caching with 53% faster response times (2.13x speedup)
- **🛒 Smart Cart System:** Database-backed persistent cart with real-time updates via AJAX
- **⚡ Fast Failover:** <500ms response time when cache unavailable
- **🎨 Seamless UX:** AJAX-powered interactions with instant feedback and notifications
- **🔄 Flexible Updates:** Real-time quantity adjustments, filtering, and checkout flow
- **📦 Production Ready:** Docker deployment, comprehensive error handling

**Inspired by:** [roadmap.sh/projects/ecommerce-api](https://roadmap.sh/projects/ecommerce-api)

---

## 🚀 Technology Stack

### Backend
- **Laravel 12.x** - Modern PHP Framework
- **PHP 8.2+** - Latest PHP features & performance
- **MySQL 8.0+** - Relational Database
- **Redis 6.0+** - In-memory cache & session storage

### Frontend
- **Blade Templates** - Server-side rendering
- **Bootstrap 5** - Responsive CSS framework
- **jQuery + AJAX** - Dynamic interactions
- **Toastr.js** - Toast notifications
- **Font Awesome** - Icon library

### Key Packages
| Package | Purpose |
|---------|---------|
| `predis/predis` | PHP Redis client for high-performance caching |
| `prettus/l5-repository` | Repository pattern implementation |
| `barryvdh/laravel-dompdf` | PDF generation (invoices, reports) |
| `intervention/image` | Image processing & optimization |
| `maatwebsite/excel` | Excel import/export |

---

## ✨ Core Features

### 🛒 Advanced Cart Management System
**Database-backed persistent cart with real-time AJAX updates**

```php
// CartService - Business Logic Layer
- Database persistence for logged-in users
- Automatic quantity validation (1-999 range)
- Cart restoration for soft-deleted items
- Real-time cart count synchronization
- Optimistic UI updates with rollback on error
```

**Key Features:**
- ✅ **Real-time Updates:** AJAX-powered add/update/remove without page reload
- ✅ **Persistent Storage:** Database-backed cart survives sessions
- ✅ **Bulk Operations:** Select and delete multiple items
- ✅ **Smart Validation:** Client & server-side quantity validation
- ✅ **Visual Feedback:** Loading animations, toast notifications, instant total recalculation
- ✅ **Responsive Design:** Optimized for mobile and desktop
- ✅ **Error Handling:** Graceful degradation with user-friendly messages

**Cart Controller Endpoints:**
```javascript
POST   /cart/add          // Add product to cart (AJAX)
PUT    /cart/update       // Update quantity (AJAX)
DELETE /cart/remove       // Remove single item (AJAX)
DELETE /cart/remove-selected  // Bulk delete (AJAX)
GET    /cart              // View cart page
```

### ⚡ Redis Caching Architecture
**High-performance caching with automatic failover**

**RedisService Features:**
- ✅ **Fast Failover:** <500ms response when Redis unavailable
- ✅ **Connection State Caching:** Checks every 5 seconds to prevent timeout spam
- ✅ **Cache-Aside Pattern:** Automatic database fallback
- ✅ **Pattern-based Deletion:** Bulk cache invalidation
- ✅ **Automatic Invalidation:** Observer pattern clears related caches on data changes

**Performance Metrics:**
| Component | Without Cache | With Redis | Speedup | Improvement |
|-----------|---------------|------------|---------|-------------|
| Homepage | 26.33ms | 13.87ms | 1.90x | 47.30% faster |
| Product Detail | 8.74ms | 1.56ms | 4.20x | 76.21% faster |
| Hot Products | 3.24ms | 2.49ms | 1.30x | 23.12% faster |
| **Average** | **12.77ms** | **5.97ms** | **2.13x** | **53.03% faster** |

**Cached Components:**
```php
// Cache TTL Strategy
SHORT  (15min): New products, hot deals, dynamic content
MEDIUM (30min): Product listings, user-specific data
LONG   (60min): Categories, static content, related products
```

**Cache Keys:**
- `home:featured_categories` - Homepage featured data
- `product_detail_{slug}` - Full product with images & category
- `product_view_stats_{id}` - View count & unique visitors
- `product_reviews_{id}_page_{n}` - Paginated reviews
- `related_products_{id}` - Same category products

### 🚀 AJAX-Powered User Experience
**Seamless interactions without page reloads**

**AJAX Features Across The Platform:**

1. **Cart Operations:**
   - Add to cart from product pages
   - Update quantities with debouncing (500ms)
   - Remove items with confirmation
   - Bulk delete selected items
   - Real-time total recalculation

2. **Product Filtering:**
   - Category filter with instant results
   - Price range slider with dynamic updates
   - Sort options (best selling, newest, price)
   - Search suggestions with autocomplete
   - No page reload - smooth transitions

3. **Checkout Process:**
   - Address validation
   - Payment method selection
   - Order confirmation with redirect
   - Real-time form validation

4. **User Profile:**
   - Avatar upload with preview
   - Profile update without reload
   - Password change with validation
   - Order status tracking

**Toast Notification System:**
```javascript
// Toastr.js Integration
Success: Green toast with checkmark
Error: Red toast with error icon
Warning: Yellow toast with warning icon
Info: Blue toast with info icon

Auto-dismiss: 2-3 seconds
Position: Top-right
Animations: Smooth fade in/out
```

### 🔄 Flexible Real-Time Updates
**Dynamic content updates without compromising performance**

**Update Strategies:**

1. **Optimistic UI Updates:**
   - Instant visual feedback
   - Background API call
   - Rollback on error
   - Loading indicators

2. **Debounced Updates:**
   - 500ms delay for quantity input
   - Prevents excessive API calls
   - Batch updates efficiently

3. **Automatic Synchronization:**
   - Cart count in navbar updates instantly
   - Session storage sync
   - Database persistence
   - Cache invalidation on changes

4. **Observer-based Cache Invalidation:**
```php
ProductObserver      → Clear product caches
CategoryObserver     → Clear homepage caches
OrderObserver        → Clear best seller caches
ProductReviewObserver → Clear review caches
```

### 🎨 Enhanced User Experience

**Visual Feedback:**
- ✅ Loading spinners for async operations
- ✅ Smooth animations and transitions
- ✅ Toast notifications for all actions
- ✅ Inline validation errors
- ✅ Disabled states during processing
- ✅ Progress indicators for multi-step processes

**Responsive Design:**
- ✅ Mobile-first approach
- ✅ Touch-friendly controls
- ✅ Adaptive layouts
- ✅ Optimized images
- ✅ Fast page loads

### 📦 Product & Order Management
- ✅ Complete CRUD operations
- ✅ Image upload with automatic optimization
- ✅ Category hierarchy
- ✅ Stock management
- ✅ Order tracking & status workflow
- ✅ PDF invoice generation
- ✅ Email notifications
- ✅ Excel import/export

### 🔐 Security & Authentication
- ✅ Session-based authentication
- ✅ Role-based access control (Admin, Customer)
- ✅ CSRF protection
- ✅ XSS prevention
- ✅ SQL injection prevention
- ✅ Input validation & sanitization
- ✅ Redis timeout protection

---

## 🏗️ Architecture & Design Patterns

### Layered Architecture
```
┌─────────────────────────────────────┐
│   Presentation Layer (Controllers)   │ ← HTTP Requests/Responses
├─────────────────────────────────────┤
│   Business Logic (Services)         │ ← Business Rules & Processing
├─────────────────────────────────────┤
│   Data Access (Repositories)        │ ← Database Operations
├─────────────────────────────────────┤
│   Cache Layer (Redis)               │ ← Performance Optimization
├─────────────────────────────────────┤
│   Models & Database                 │ ← Data Storage
└─────────────────────────────────────┘
```

### Repository Pattern
**Clean separation of concerns:**
```php
// Interface defines contract
interface CartRepositoryInterface {
    public function getByUser(int $userId);
    public function updateQuantity(int $cartId, int $quantity);
}

// Implementation handles data access
class CartRepository extends BaseRepository {
    public function model() {
        return Cart::class;
    }
}
```

### Service Layer Pattern
**Business logic isolation:**
```php
// CartService handles all cart business logic
class CartService {
    public function addToCart($userId, $productId, $quantity)
    {
        // Validation, business rules, transactions
        // Calls repository for data operations
    }
}
```

### Observer Pattern
**Automatic cache invalidation:**
```php
class ProductObserver {
    public function updated(Product $product) {
        // Clear all related caches automatically
        RedisService::forget("product_detail_{$product->slug}");
        RedisService::deleteByPattern("product_reviews_{$product->id}_*");
    }
}
```

### Cache-Aside Pattern
**High-performance caching with fallback:**
```php
$data = $redis->remember('cache_key', 3600, function() {
    // If cache miss, fetch from database
    return Product::with('images')->find($id);
});
```

---

## 📈 Performance & Optimization

### Redis Caching Strategy

**Cache TTL Tiers:**
```php
SHORT  = 900s  (15min)  // Frequently changing data
MEDIUM = 1800s (30min)  // Moderate update frequency  
LONG   = 3600s (60min)  // Stable content
```

**Connection Management:**
- Fast timeout: 500ms (no long delays)
- Zero retries (fail fast)
- Connection state caching (5s interval)
- Automatic database fallback

**Performance Impact:**
| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| Response Time | 12.77ms | 5.97ms | **53% faster** |
| Homepage Load | 26.33ms | 13.87ms | **47% faster** |
| Product Detail | 8.74ms | 1.56ms | **76% faster** |
| Throughput | 1000 req/s | 2130 req/s | **2.13x** |

### Database Optimization

**Indexes:**
```sql
-- Products table
INDEX idx_category_status (category_id, status)
INDEX idx_price (price)
COMPOSITE INDEX (status, category_id, price)
```

**Query Optimization:**
- Eager loading (prevents N+1 queries)
- Subquery aggregations
- Pagination with cursor-based loading
- Query result caching

### Frontend Optimization

**AJAX Benefits:**
- No full page reloads
- Partial DOM updates
- Optimistic UI rendering
- Background data fetching
- Debounced user inputs

**Asset Optimization:**
- Image lazy loading
- WebP conversion
- Vite for bundling & minification
- CDN-ready architecture

---

## 🛠️ Installation & Setup

### System Requirements
- **PHP** >= 8.2
- **Composer** >= 2.x
- **Node.js** >= 18.x
- **MySQL** >= 8.0
- **Redis** >= 6.0

### Quick Start

```bash
# 1. Clone repository
git clone https://github.com/dqhuy2005/ModernWebShop.git
cd ModernWebShop

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Configure .env
DB_DATABASE=modernwebshop
REDIS_CLIENT=predis
CACHE_DRIVER=redis

# 5. Database setup
php artisan migrate --seed

# 6. Storage & assets
php artisan storage:link
npm run build

# 7. Start services
# Terminal 1: Redis
redis-server

# Terminal 2: Laravel
php artisan serve
```

**Default Accounts:**
- Admin: `admin@example.com` / `password`
- User: `user@example.com` / `password`

### Production Deployment

**With Docker:**
```bash
docker-compose up -d
```

**Manual Deployment:**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

---

## 📂 Project Structure

```
app/
├── Http/Controllers/
│   ├── User/              # Customer-facing controllers
│   │   ├── CartController.php       # Cart operations (AJAX)
│   │   ├── CheckoutController.php   # Checkout process
│   │   └── ProfileController.php    # User profile management
│   └── CMS/               # Admin controllers
│       ├── ProductController.php
│       └── OrderController.php
├── Services/              # Business logic layer
│   └── impl/
│       ├── RedisService.php         # Cache management
│       └── CartService.php          # Cart business logic
├── Repositories/          # Data access layer
│   ├── Contracts/         # Repository interfaces
│   └── Eloquent/          # Eloquent implementations
├── Observers/             # Model observers (cache invalidation)
│   ├── ProductObserver.php
│   └── CategoryObserver.php
├── Models/                # Eloquent models
│   ├── Product.php
│   ├── Cart.php
│   └── Order.php
└── DTOs/                  # Data Transfer Objects

resources/
├── views/
│   ├── user/              # Customer views
│   │   ├── cart.blade.php           # Cart page (AJAX-powered)
│   │   ├── checkout.blade.php       # Checkout flow
│   │   └── category.blade.php       # Product filtering
│   └── layouts/           # Layout templates
└── js/                    # Frontend JavaScript

config/
├── database.php           # Database & Redis config
└── cache.php              # Cache driver settings
```

---

## 🔧 Development Tools

### Useful Commands

```bash
# Cache management
php artisan cache:clear              # Clear application cache
php artisan config:cache             # Cache configuration
php artisan route:cache              # Cache routes
php artisan view:cache               # Cache views

# Redis operations
php artisan tinker
>>> app(\App\Services\impl\RedisService::class)->isRedisAvailable()

# Code quality
./vendor/bin/pint                    # Format code (Laravel Pint)

# Logs
php artisan pail                     # Real-time log viewer

# Database
php artisan migrate:fresh --seed     # Fresh migration with data
```

### Testing

```bash
# Run tests (for development reference)
php artisan test
```

*Note: Testing is maintained for development purposes. Focus is on production features.*

---

## 🔒 Security & Best Practices

### Security Features
- ✅ CSRF token validation on all forms
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade auto-escaping)
- ✅ Password hashing (Bcrypt)
- ✅ Input validation & sanitization
- ✅ File upload validation
- ✅ Rate limiting on API endpoints
- ✅ Redis timeout protection

### Code Quality
- **Repository Pattern:** Clean data access abstraction
- **Service Layer:** Centralized business logic
- **Observer Pattern:** Automatic cache management
- **Dependency Injection:** Testable and maintainable code
- **PSR Standards:** Following PHP coding standards

---

## 🚀 Redis Configuration

### Optimized Settings (`config/database.php`)
```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),
    'options' => [
        'parameters' => [
            'read_write_timeout' => 0.5,  // 500ms timeout
            'timeout' => 0.5,              // Fast failover
        ],
    ],
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', '6379'),
        'max_retries' => 0,                // Fail fast strategy
    ],
],
```

### Cache Invalidation Flow
```
Data Update → Observer Triggered → Cache Cleared → Fresh Data Cached
```

---

## 🤝 Contributing

Contributions welcome! Follow these guidelines:

1. Fork the repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open Pull Request

**Code Style:**
```bash
./vendor/bin/pint    # Format code before committing
```

---

## 📄 License

MIT License - see [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Dang Quoc Huy**
- GitHub: [@dqhuy2005](https://github.com/dqhuy2005)
- Email: dangqhuy091245@gmail.com
- Project: [ModernWebShop](https://github.com/dqhuy2005/ModernWebShop)

---

## 🙏 Acknowledgments

- [Laravel Framework](https://laravel.com) - Modern PHP framework
- [Redis](https://redis.io) - In-memory data store
- [Bootstrap](https://getbootstrap.com) - Frontend framework
- [roadmap.sh](https://roadmap.sh/projects/ecommerce-api) - Project inspiration

---

## 📞 Support

For issues or questions:
1. Check existing [Issues](https://github.com/dqhuy2005/ModernWebShop/issues)
2. Create new issue with detailed description
3. Contact via email

---

<p align="center">
  <b>Built by Dang Quoc Huy</b><br>
</p>
