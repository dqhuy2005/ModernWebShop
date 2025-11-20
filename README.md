# 🛒 ModernWebShop - E-Commerce Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red.svg" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
  <img src="https://img.shields.io/badge/Status-Active-success.svg" alt="Status">
</p>

## 📖 Giới Thiệu

**ModernWebShop** là một hệ thống thương mại điện tử (E-Commerce) đầy đủ tính năng, được xây dựng trên nền tảng **Laravel 12** với kiến trúc MVC hiện đại. Dự án được thiết kế để quản lý toàn bộ quy trình bán hàng trực tuyến, từ quản lý sản phẩm, đơn hàng, đến xử lý thanh toán và xuất báo cáo.

### 🎯 Mục Tiêu Dự Án
- Cung cấp giải pháp E-Commerce hoàn chỉnh cho doanh nghiệp vừa và nhỏ
- Áp dụng các design pattern và best practices của Laravel
- Tối ưu hóa performance với Repository Pattern và Query Optimization
- Hỗ trợ đa vai trò: Admin, Staff, Customer

**Project Inspiration:** [roadmap.sh/projects/ecommerce-api](https://roadmap.sh/projects/ecommerce-api)

---

## 🚀 Công Nghệ Sử Dụng

### Core Framework
- **Laravel 12.x** - PHP Framework chính
- **PHP 8.2+** - Programming Language
- **MySQL 8.0+** - Database Management System
- **Redis** - In-memory data structure store (Caching & Session)
- **Vite** - Frontend Build Tool

### Frontend Technologies
- **Bootstrap 5** - CSS Framework
- **jQuery** - JavaScript Library
- **Toastr.js** - Notification System
- **Font Awesome** - Icon Library
- **Blade Templates** - Laravel Templating Engine

### Development Tools
- **Laravel Pail** - Real-time log viewer
- **Laravel Debugbar** - Debug toolbar
- **Laravel Pint** - Code style fixer
- **PHPUnit** - Testing framework

---

## 📦 Các Package & Mục Đích Sử Dụng

### Authentication & Authorization
| Package | Version | Mục Đích |
|---------|---------|----------|
| `laravel/socialite` | ^5.23 | OAuth login (Google, Facebook, GitHub) |

### Data Management
| Package | Version | Mục Đích |
|---------|---------|----------|
| `prettus/l5-repository` | ^3.0 | Repository Pattern implementation, tách biệt business logic khỏi data access |
| `maatwebsite/excel` | latest | Import/Export Excel files cho sản phẩm, đơn hàng, báo cáo |
| `predis/predis` | ^2.0 | Redis client cho PHP, cache management và session storage |

### PDF & Document Generation
| Package | Version | Mục Đích |
|---------|---------|----------|
| `barryvdh/laravel-dompdf` | ^3.1 | Tạo PDF cho hóa đơn, báo cáo, phiếu xuất kho |

### Image Processing
| Package | Version | Mục Đích |
|---------|---------|----------|
| `intervention/image` | ^3.11 | Resize, crop, optimize ảnh sản phẩm, avatar, thumbnails |

### Development Packages
| Package | Version | Mục Đích |
|---------|---------|----------|
| `barryvdh/laravel-debugbar` | ^3.16 | Debug queries, performance profiling |
| `laravel/pail` | ^1.2.2 | Real-time log streaming trong terminal |
| `fakerphp/faker` | ^1.23 | Generate fake data cho seeding & testing |

---

## 🏗️ Cấu Trúc Dự Án

```
backend/
├── app/
│   ├── Console/              # Artisan commands
│   ├── Events/               # Application events
│   ├── Exceptions/           # Custom exception handlers
│   ├── Exports/              # Excel export classes
│   ├── Helpers/              # Helper functions & utilities
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CMS/          # Admin/CMS controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   └── UserController.php
│   │   │   └── User/         # Customer-facing controllers
│   │   │       ├── HomeController.php
│   │   │       ├── ProfileController.php
│   │   │       ├── CartController.php
│   │   │       ├── CheckoutController.php
│   │   │       └── PurchaseController.php
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form request validation
│   │       └── ProductFilterRequest.php
│   ├── Imports/              # Excel import classes
│   ├── Listeners/            # Event listeners
│   ├── Models/               # Eloquent models
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── OrderDetail.php
│   │   ├── Cart.php
│   │   ├── Role.php
│   │   ├── CacheKeyManager.php
│   │   └── RefreshToken.php
│   ├── Providers/            # Service providers
│   ├── Repository/           # Repository layer (Data Access)
│   │   ├── BaseRepository.php
│   │   ├── ProductRepository.php
│   │   ├── CategoryRepository.php
│   │   ├── OrderRepository.php
│   │   ├── CartRepository.php
│   │   └── UserRepository.php
│   ├── Services/             # Business logic layer
│   │   ├── AuthService.php
│   │   ├── ImageService.php
│   │   ├── HomePageService.php
│   │   ├── ProductViewService.php
│   │   └── RedisService.php
│   └── Observers/            # Model observers (Cache invalidation)
│       ├── ProductObserver.php
│       ├── ProductReviewObserver.php
│       ├── CategoryObserver.php
│       └── OrderObserver.php
├── bootstrap/                # Framework bootstrap
├── config/                   # Configuration files
│   ├── app.php
│   ├── auth.php
│   └── database.php
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
│       ├── CategorySeeder.php
│       ├── ProductSeeder.php
│       ├── RoleSeeder.php
│       └── UserSeeder.php
├── public/                   # Public assets
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── imgs/
│   └── storage/              # Symlink to storage/app/public
├── resources/
│   ├── css/                  # Source CSS files
│   ├── js/                   # Source JS files
│   └── views/                # Blade templates
│       ├── admin/            # Admin panel views
│       ├── user/             # Customer views
│       ├── layouts/
│       │   ├── admin/
│       │   │   └── app.blade.php
│       │   └── user/
│       │       └── app.blade.php
│       └── components/       # Reusable components
├── routes/
│   ├── web.php               # Web routes
│   ├── api.php               # API routes
│   └── console.php           # Console routes
├── storage/                  # Storage directory
│   ├── app/
│   │   └── public/           # Public storage
│   ├── framework/
│   └── logs/
├── tests/                    # PHPUnit tests
├── .env.example              # Environment variables template
├── composer.json             # PHP dependencies
├── package.json              # NPM dependencies
└── artisan                   # Artisan CLI
```

---

## ✨ Các Feature/Chức Năng Chính

### 🔐 Authentication & Authorization
- ✅ Session-based web authentication
- ✅ Role-based access control (Admin, Staff, Customer)
- ✅ OAuth login (Google, Facebook)
- ✅ Password reset & email verification

### 👤 User Management (Admin)
- ✅ CRUD operations cho users
- ✅ Role assignment & permissions
- ✅ Soft delete users
- ✅ User activity logging
- ✅ Profile management với avatar upload

### 📦 Product Management
- ✅ CRUD operations cho products
- ✅ Multiple product images
- ✅ Category hierarchy (parent-child)
- ✅ Product specifications (JSON field)
- ✅ Stock management
- ✅ Hot deals/featured products
- ✅ Product search & filtering
- ✅ Advanced sorting (best selling, newest, price)
- ✅ Image optimization & thumbnails

### 🛍️ Shopping Experience
- ✅ Product listing với pagination
- ✅ Advanced filtering (price range, category)
- ✅ Quick sort tags (Bán chạy, Mới nhất)
- ✅ AJAX-based filtering (no page reload)
- ✅ Search suggestions với autocomplete
- ✅ Product view tracking
- ✅ Related products

### 🛒 Cart & Checkout
- ✅ Add/update/remove cart items
- ✅ Cart persistence (database-backed)
- ✅ Real-time cart calculations
- ✅ Guest cart support
- ✅ Multi-step checkout process
- ✅ Order summary & review
- ✅ Multiple payment methods

### 📋 Order Management
- ✅ Order creation & tracking
- ✅ Order status workflow (pending → processing → completed)
- ✅ Order details với line items
- ✅ Order history cho customers
- ✅ Admin order management dashboard
- ✅ PDF invoice generation
- ✅ Email notifications

### 📊 Reporting & Analytics
- ✅ Sales reports (daily, monthly, yearly)
- ✅ Product performance analytics
- ✅ Best selling products
- ✅ Customer insights
- ✅ Revenue tracking
- ✅ Export to Excel/PDF

### 🖼️ Image Management
- ✅ Multiple image upload
- ✅ Automatic resize & optimization
- ✅ Thumbnail generation
- ✅ WebP conversion support
- ✅ Image validation (size, type)

### 🔍 Search & Filter
- ✅ Full-text search
- ✅ Search suggestions API
- ✅ Advanced filtering system
- ✅ Price range filter
- ✅ Category filter
- ✅ Sort by multiple criteria

### ⚡ Performance & Caching
- ✅ Redis caching implementation
- ✅ Cache-aside pattern với automatic fallback
- ✅ Fast failover (<500ms) khi Redis offline
- ✅ Connection state caching (5s interval)
- ✅ Automatic cache invalidation via Observers
- ✅ Homepage data caching (47% faster)
- ✅ Product detail caching (76% faster, 4.2x speedup)
- ✅ Review & statistics caching
- ✅ Cache warming strategies
- ✅ Query optimization với eager loading

### 🎨 UI/UX Features
- ✅ Responsive design (mobile-first)
- ✅ Loading overlays & indicators
- ✅ Toast notifications (success, error, warning)
- ✅ Form validation với inline errors
- ✅ Modal dialogs
- ✅ Breadcrumb navigation
- ✅ Pagination với meta data

---

## 🛠️ Cách Setup/Cài Đặt

### Yêu Cầu Hệ Thống

- **PHP** >= 8.2
- **Composer** >= 2.x
- **Node.js** >= 18.x & NPM
- **MySQL** >= 8.0 hoặc MariaDB >= 10.3
- **Redis** >= 6.0 (recommended for caching)
- **Git**

### Các Extension PHP Cần Thiết

```bash
php-mbstring
php-xml
php-curl
php-zip
php-gd (cho image processing)
php-mysql (hoặc php-pdo-mysql)
php-bcmath (cho tính toán số thập phân)
```

### Bước 1: Clone Repository

```bash
git clone https://github.com/dqhuy2005/ModernWebShop.git
cd ModernWebShop/backend
```

### Bước 2: Cài Đặt Dependencies

```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt NPM dependencies
npm install
```

### Bước 3: Cấu Hình Environment

```bash
# Copy file .env.example
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Bước 4: Cấu Hình Database

Mở file `.env` và cập nhật thông tin database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=modernwebshop
DB_USERNAME=root
DB_PASSWORD=your_password

# Redis Configuration
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_DRIVER=redis
```

Tạo database:

```bash
# Trong MySQL console
CREATE DATABASE modernwebshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Bước 5: Run Migrations & Seeders

```bash
# Chạy migrations
php artisan migrate

# Chạy seeders (tạo data mẫu)
php artisan db:seed
```

**Default Accounts sau khi seed:**

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Customer | user@example.com | password |

### Bước 6: Tạo Storage Symlink

```bash
php artisan storage:link
```

### Bước 7: Build Frontend Assets

```bash
# Development build
npm run dev

# Production build
npm run build
```

### Bước 8: Start Development Server

**Start Redis Server (if not running):**
```bash
# Windows (if installed as service)
redis-server

# Or using Docker
docker run -d -p 6379:6379 redis:alpine
```

**Option 1: PHP Built-in Server**
```bash
php artisan serve
```
Truy cập: http://localhost:8000

**Option 2: Laravel Sail (Docker)**
```bash
./vendor/bin/sail up
```

**Option 3: Concurrent Development (Recommended)**
```bash
composer run dev
```
Lệnh này sẽ chạy đồng thời:
- PHP server (port 8000)
- Queue worker
- Log viewer (Pail)
- Vite dev server

---

## 🧪 Testing

```bash
# Chạy tất cả tests
php artisan test

# Chạy test với coverage
php artisan test --coverage

# Chạy specific test file
php artisan test tests/Feature/ProductTest.php
```

---

## 🔧 Useful Commands

### Development

```bash
# Clear all caches
php artisan optimize:clear

# Clear Redis cache specifically
php artisan cache:clear

# Check Redis connection
php artisan tinker
>>> app(\App\Services\RedisService::class)->ping()

# Generate IDE helper files
php artisan ide-helper:generate

# Run code style fixer
./vendor/bin/pint

# View real-time logs
php artisan pail
```

### Database

```bash
# Fresh migration với seed
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

### Queue & Jobs

```bash
# Start queue worker
php artisan queue:work

# List failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

## 📂 Key Configuration Files

### Redis Configuration (`config/database.php`)
```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),
    'options' => [
        'parameters' => [
            'read_write_timeout' => 0.5,  // 500ms timeout
            'timeout' => 0.5,              // Fast connection timeout
        ],
    ],
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', '6379'),
        'max_retries' => 0,                // Fail fast, no retries
    ],
],
```

### Database Configuration
Sử dụng **MySQL** với strict mode:
- `ONLY_FULL_GROUP_BY` enabled
- UTF8MB4 character set
- InnoDB engine

---

## 🏛️ Kiến Trúc & Design Patterns

### Repository Pattern
Tách biệt business logic khỏi data access layer:
```php
// Repository Interface
interface ProductRepositoryInterface {
    public function getFilteredProducts($categoryId, $filters);
}

// Implementation
class ProductRepository extends BaseRepository {
    public function model() {
        return Product::class;
    }
}
```

### Service Layer
Xử lý business logic phức tạp:
```php
class AuthService {
    public function login($credentials);
    public function register($data);
    public function logout();
}

class RedisService {
    public function remember($key, $ttl, $callback);
    public function get($key, $default = null);
    public function set($key, $value, $ttl = null);
    public function forget($keys);
    public function isRedisAvailable(); // Fast failover
}
```

### Observer Pattern
Automatic cache invalidation khi data thay đổi:
```php
class ProductObserver {
    public function updated(Product $product) {
        // Clear related caches
        $this->redis->forget("product_detail_{$product->slug}");
        $this->redis->forget("product_view_stats_{$product->id}");
        $this->redis->deleteByPattern("product_reviews_{$product->id}_*");
    }
}
```

### Model Relationships
Sử dụng Eloquent ORM relationships:
- One-to-Many: Category → Products
- Many-to-One: Product → Category
- One-to-Many: Order → OrderDetails
- Many-to-Many: User → Roles

### Query Optimization
- Eager loading để tránh N+1 query problem
- Subquery cho aggregation (best_selling products)
- Index optimization trên các column hay query

---

## 📈 Performance Optimization

### Database Indexes
```sql
-- Products table
INDEX idx_category_status (category_id, status)
INDEX idx_price (price)
INDEX idx_created_at (created_at)
COMPOSITE INDEX (status, category_id, price)
```

### Caching Strategy
- **Redis-based caching** với Predis client
- **Cache TTLs:**
  - SHORT: 900s (15min) - Frequently changing data
  - MEDIUM: 1800s (30min) - Moderate update frequency
  - LONG: 3600s (1hr) - Stable data
- **Cache layers:**
  - Homepage data (categories, products, deals)
  - Product details with relationships
  - Product view statistics
  - Reviews and review statistics
  - Related products
- **Automatic cache invalidation** via Model Observers
- **Fast failover** (<500ms) when Redis unavailable
- **Connection state caching** (5s interval) to prevent repeated timeouts
- Route caching: `php artisan route:cache`
- Config caching: `php artisan config:cache`
- View caching: `php artisan view:cache`

### Performance Metrics
- **Homepage caching:** 47.30% faster (1.90x speedup)
- **Product detail caching:** 76.21% faster (4.20x speedup)
- **Hot products caching:** 23.12% faster (1.30x speedup)
- **Overall improvement:** 53.03% faster (2.13x speedup)
- **Redis failover:** <500ms response time when offline

### Image Optimization
- Resize ảnh về multiple sizes (thumbnail, medium, large)
- WebP conversion cho modern browsers
- Lazy loading images
- CDN integration support

---

## 🔒 Security Features

- ✅ CSRF protection
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade escaping)
- ✅ Password hashing (Bcrypt)
- ✅ JWT token validation
- ✅ Rate limiting
- ✅ Input validation & sanitization
- ✅ Secure file upload validation
- ✅ Redis timeout protection (prevents long delays)

---

## 🚀 Redis Caching Architecture

### Cache Implementation

**RedisService** (`app/Services/RedisService.php`)
- Centralized Redis operations handler
- Connection state caching (prevents repeated timeouts)
- Fast failover mechanism (<500ms when Redis offline)
- Automatic serialization/deserialization
- Pattern-based cache deletion

**Key Methods:**
```php
remember($key, $ttl, $callback)  // Cache-aside pattern
get($key, $default)              // Get with fallback
set($key, $value, $ttl)          // Set with expiration
forget($keys)                     // Delete single/multiple keys
deleteByPattern($pattern)         // Bulk delete by pattern
isRedisAvailable()               // Connection check with caching
```

### Cached Components

| Component | Cache Key | TTL | Description |
|-----------|-----------|-----|-------------|
| Homepage Featured | `home:featured_categories` | 3600s | Featured categories with products |
| New Products | `home:new_products` | 900s | Latest 8 products |
| Hot Deals | `home:hot_deals` | 1800s | Promotional products |
| Product Detail | `product_detail_{slug}` | 600s | Full product with images & category |
| Product Views | `product_view_stats_{id}` | 300s | View count & unique visitors |
| Reviews | `product_reviews_{id}_page_{n}` | 600s | Paginated reviews |
| Review Stats | `product_review_stats_{id}` | 600s | Average rating & count |
| Related Products | `related_products_{id}` | 3600s | Same category products |

### Automatic Cache Invalidation

**Observers** handle cache clearing when data changes:

```php
// ProductObserver
- Clear product detail cache on update
- Clear view statistics
- Clear all review pages
- Clear related products

// ProductReviewObserver  
- Clear review caches when review added/updated
- Clear review statistics

// CategoryObserver
- Clear category caches on update
- Clear homepage caches

// OrderObserver
- Clear best seller caches on order update
```

### Performance Benefits

**With Redis Online:**
- Homepage: 13.87ms (vs 26.33ms without cache)
- Product Detail: 1.56ms (vs 8.74ms without cache)
- Real-world: Saves 20.43s per 1000 users

**With Redis Offline:**
- Fast failover in <500ms
- Automatic database fallback
- No long delays (10-30s eliminated)
- Connection state cached for 5s

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

**Code Style:** Sử dụng Laravel Pint để format code
```bash
./vendor/bin/pint
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Author

**Dang Quoc Huy**
- GitHub: [@dqhuy2005](https://github.com/dqhuy2005)
- Email: dangqhuy091245@gmail.com

---

## 🙏 Acknowledgments

- [Laravel Framework](https://laravel.com)
- [Bootstrap](https://getbootstrap.com)
- [Font Awesome](https://fontawesome.com)
- [roadmap.sh](https://roadmap.sh) - Project inspiration

---

## 📞 Support

Nếu bạn gặp vấn đề hoặc có câu hỏi, vui lòng:
1. Kiểm tra [Issues](https://github.com/dqhuy2005/ModernWebShop/issues) hiện có
2. Tạo Issue mới với mô tả chi tiết
3. Liên hệ qua email

---

<p align="center">Made with ❤️ by Dang Quoc Huy</p>
