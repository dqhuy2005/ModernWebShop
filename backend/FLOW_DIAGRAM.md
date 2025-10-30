# 🔄 Product View Tracking - Complete Flow Diagram

## 📊 Overview Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          USER REQUEST FLOW                                   │
└─────────────────────────────────────────────────────────────────────────────┘

User Browser
     │
     │ [1] GET /products/{slug}
     ▼
┌──────────────────────┐
│   Laravel Router     │
│  (routes/web.php)    │
└──────────────────────┘
     │
     │ [2] Route to Controller
     ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                   ProductController@show                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│  [3] Get product from cache/DB (Cache: 10 min)                              │
│  [4] Call ProductViewService->trackView()                                    │
│  [5] Get view statistics (cached)                                            │
│  [6] Return view to user                                                     │
└─────────────────────────────────────────────────────────────────────────────┘
     │                                        │
     │ [6] Return HTML                        │ [4] Track view
     ▼                                        ▼
User sees product page          ┌──────────────────────────┐
(No delay, instant response)    │  ProductViewService      │
                                 │  ->trackView()           │
                                 └──────────────────────────┘
                                        │
                                        │ [7] Check anti-spam
                                        ▼
                          ┌───────────────────────────────┐
                          │  Cache Check (2 minutes)      │
                          │  Key: product_view_{id}_{ip}  │
                          └───────────────────────────────┘
                                        │
                          ┌─────────────┴─────────────┐
                          │                           │
                    [Spam detected]            [Valid view]
                          │                           │
                          ▼                           ▼
                    Return false          ┌──────────────────────┐
                    (Silent skip)         │ Set cache (2 min)    │
                                          │ Dispatch Event       │
                                          └──────────────────────┘
                                                  │
                                                  │ [8] Event dispatched
                                                  ▼
                                    ┌─────────────────────────────┐
                                    │   ProductViewed Event       │
                                    │   (Queued for async)        │
                                    └─────────────────────────────┘
                                                  │
                                                  │ [9] Pushed to Queue
                                                  ▼
                                    ┌─────────────────────────────┐
                                    │      Laravel Queue          │
                                    │   (database/redis/etc)      │
                                    └─────────────────────────────┘
                                                  │
                                                  │ [10] Queue Worker picks up
                                                  ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                   UpdateProductHotStatus Listener                            │
│                   (Async Background Processing)                              │
├─────────────────────────────────────────────────────────────────────────────┤
│  [11] Create ProductView record                                              │
│       - product_id, ip_address, user_agent, user_id, viewed_at              │
│                                                                               │
│  [12] Increment Product.view_count                                           │
│       - $product->increment('view_count')                                    │
│                                                                               │
│  [13] Calculate recent views (7 days)                                        │
│       - Query ProductView table (cached 5 min)                               │
│       - WHERE viewed_at >= now() - 7 days                                    │
│                                                                               │
│  [14] Update is_hot status                                                   │
│       IF recent_views >= 100 → is_hot = true                                 │
│       IF recent_views < 50   → is_hot = false                                │
│       IF 50 <= recent_views < 100 → no change                                │
│                                                                               │
│  [15] Clear relevant caches                                                  │
│       - Product detail cache                                                 │
│       - Hot products list cache                                              │
│       - Recent views cache                                                   │
│                                                                               │
│  [16] Log success                                                            │
│       - Product ID, recent views, new is_hot status                          │
└─────────────────────────────────────────────────────────────────────────────┘
     │
     │ [17] Job completed
     ▼
Queue: Job removed
```

---

## 🎯 Detailed Step-by-Step Flow

### Phase 1: User Request (Synchronous - Instant)

```
┌────────┐     GET /products/iphone-15     ┌────────────┐
│ User   │ ─────────────────────────────▶ │  Laravel   │
└────────┘                                  │  Router    │
                                            └────────────┘
                                                  │
                                                  │ Match route
                                                  ▼
                                       ┌───────────────────────┐
                                       │ ProductController     │
                                       │ @show('iphone-15')    │
                                       └───────────────────────┘
```

**Time: ~0ms** - Route matching is instant

---

### Phase 2: Controller Processing (Synchronous - Fast)

```
ProductController@show()
│
├─[1] Get product from cache/database
│    Cache::remember("product_detail_iphone-15", 10min)
│    ✅ Cache hit: 0-5ms | ❌ Cache miss: 20-50ms (DB query)
│
├─[2] Track view (non-blocking)
│    $viewService->trackView($product, $ip, $userAgent)
│    ✅ Returns immediately (0-5ms)
│    └─ Event dispatched to queue (async)
│
├─[3] Get related products
│    Cache::remember("related_products_{id}", 1hr)
│    ✅ Cache hit: 0-5ms | ❌ Cache miss: 10-30ms
│
├─[4] Get view statistics
│    - total_views: from $product->view_count (cached)
│    - recent_views_7days: cached 5 min (0-5ms)
│    - unique_visitors: cached 10 min (0-5ms)
│    ✅ All from cache: 0-10ms total
│
└─[5] Return view to user
     view('user.product-detail', compact(...))
     ✅ Rendered HTML: 10-50ms

TOTAL RESPONSE TIME: 50-150ms (excellent UX!)
```

**User sees the page immediately - No blocking!**

---

### Phase 3: View Tracking Service (Synchronous - Fast)

```
ProductViewService->trackView()
│
├─[1] Check anti-spam
│    $cacheKey = "product_view_{$productId}_ip_{md5($ip)}"
│    if (Cache::has($cacheKey)) {
│        return false; // Skip silently
│    }
│    ✅ Fast: 0-5ms
│
├─[2] Valid view detected
│    Cache::put($cacheKey, true, now()->addMinutes(2))
│    ✅ Set 2-minute lock: 0-5ms
│
└─[3] Dispatch event (non-blocking!)
     event(new ProductViewed($product, $ip, $userAgent, $userId))
     ✅ Pushed to queue: 0-10ms
     └─ Returns immediately (does NOT wait for processing)

TOTAL TIME: 5-20ms (non-blocking!)
```

---

### Phase 4: Event & Queue (Asynchronous - Background)

```
ProductViewed Event
│
├─ Implements: SerializesModels
├─ Properties: product, ipAddress, userAgent, userId
│
└─ Pushed to Queue
   │
   ├─ Queue Driver: database/redis/sqs/etc
   ├─ Job serialized and stored
   └─ Queue worker picks up when available

Queue Worker (running in background)
│
├─ php artisan queue:work
├─ Picks up job
└─ Executes UpdateProductHotStatus listener
```

**This happens in the background - User is already viewing the product!**

---

### Phase 5: Listener Processing (Asynchronous - Background)

```
UpdateProductHotStatus->handle()
│
├─[1] START (Background job)
│    Log: "Processing product view for product {id}"
│
├─[2] Create ProductView record
│    ProductView::create([
│        'product_id' => $product->id,
│        'ip_address' => $event->ipAddress,
│        'user_agent' => $event->userAgent,
│        'user_id' => $event->userId,
│        'viewed_at' => now(),
│    ]);
│    ✅ DB INSERT: 10-50ms
│
├─[3] Increment view_count
│    $product->increment('view_count');
│    ✅ DB UPDATE: 5-20ms
│
├─[4] Calculate recent views (7 days)
│    $recentViews = Cache::remember(
│        "product_{$id}_recent_views",
│        5min,
│        fn() => ProductView::where('product_id', $id)
│                 ->where('viewed_at', '>=', now()->subDays(7))
│                 ->count()
│    );
│    ✅ Cache hit: 0-5ms | ❌ Cache miss: 50-200ms (DB query with index)
│
├─[5] Update is_hot status
│    if ($recentViews >= 100) {
│        $product->is_hot = true;
│    } elseif ($recentViews < 50) {
│        $product->is_hot = false;
│    }
│    // 50-99: no change
│    $product->save();
│    ✅ DB UPDATE: 5-20ms (only if changed)
│
├─[6] Clear caches
│    Cache::forget("product_detail_{$slug}");
│    Cache::forget("hot_products");
│    Cache::forget("product_{$id}_recent_views");
│    ✅ Fast: 0-10ms
│
├─[7] Log success
│    Log::info("Product hot status updated", [
│        'product_id' => $id,
│        'recent_views' => $recentViews,
│        'is_hot' => $product->is_hot,
│    ]);
│
└─[8] COMPLETE
     Queue: Job removed from queue

TOTAL BACKGROUND TIME: 100-350ms
```

**Error Handling:**
```
try {
    // ... process ...
} catch (\Exception $e) {
    Log::error("Failed to update hot status", [
        'product_id' => $product->id,
        'error' => $e->getMessage(),
    ]);
    throw $e; // Re-throw for queue retry
}
```

**Retry Logic:**
- Max tries: 3
- Backoff: [10s, 30s, 60s]
- If all retries fail → job moved to `failed_jobs` table

---

## ⚡ Performance Optimization Points

### 1. **Caching Strategy**

```
┌─────────────────────────────────────────────────┐
│           Cache Layers & TTL                     │
├─────────────────────────────────────────────────┤
│ Anti-spam lock        : 2 minutes               │
│ Recent views (7 days) : 5 minutes               │
│ Product detail        : 10 minutes              │
│ Unique visitors       : 10 minutes              │
│ Related products      : 1 hour                  │
│ Hot products list     : 1 hour                  │
└─────────────────────────────────────────────────┘
```

### 2. **Database Indexes**

```sql
-- Optimized for 7-day queries
CREATE INDEX idx_product_views_product_date 
    ON product_views(product_id, viewed_at);

CREATE INDEX idx_product_views_product_ip_date 
    ON product_views(product_id, ip_address, viewed_at);

CREATE INDEX idx_product_views_product_user_date 
    ON product_views(product_id, user_id, viewed_at);
```

**Query Performance:**
- Without index: 500-2000ms (full table scan)
- With index: 10-50ms (index seek) ✅

---

### 3. **Async Processing**

```
┌────────────────────────────────────────────────────────┐
│                Sync vs Async Comparison                 │
├────────────────────────────────────────────────────────┤
│                                                         │
│  Synchronous (❌ Bad):                                  │
│  User Request → DB Insert → DB Update → Count Query    │
│              → Update is_hot → Clear Cache              │
│              → Return Response                          │
│  TOTAL: 200-500ms (user waits!)                        │
│                                                         │
│  Asynchronous (✅ Good):                                │
│  User Request → Dispatch Event → Return Response       │
│  TOTAL: 50-150ms (instant!)                            │
│                                                         │
│  Background: Queue Worker handles everything else       │
│                                                         │
└────────────────────────────────────────────────────────┘
```

---

## 🔒 Anti-Spam Mechanism

```
User visits product
    │
    ├─ Check cache: product_view_{id}_ip_{hash}
    │
    ├─ EXISTS? (within 2 minutes)
    │   └─ YES → Skip tracking (silent)
    │   └─ NO  → Track view + Set cache
    │
    └─ User can still view product (no error!)
```

**Benefits:**
- ✅ No 429 errors
- ✅ No UX interruption
- ✅ Silent protection
- ✅ Prevents spam/bots

---

## 📈 Hot Product Status Logic

```
Recent Views (7 days)    →    is_hot Status
──────────────────────────────────────────────
>= 100 views             →    ✅ TRUE (HOT!)
50-99 views              →    ⏸️ UNCHANGED
< 50 views               →    ❌ FALSE

Example Timeline:
Day 0:  0 views    → is_hot = false
Day 1:  45 views   → is_hot = false
Day 2:  65 views   → is_hot = false (unchanged)
Day 3:  100 views  → is_hot = true ✅
Day 4:  105 views  → is_hot = true
Day 5:  80 views   → is_hot = true (unchanged)
Day 6:  48 views   → is_hot = false ❌
```

---

## 🎯 System Architecture Decisions

### Why Event/Listener over Observer?

```
┌────────────────────────────────────────────────────┐
│          Observer vs Event/Listener                 │
├────────────────────────────────────────────────────┤
│                                                     │
│  Observer Pattern:                                  │
│  ❌ Synchronous only                                │
│  ❌ Hard to test                                    │
│  ❌ Always runs (can't disable)                     │
│  ❌ Harder to queue                                 │
│                                                     │
│  Event/Listener Pattern:                            │
│  ✅ Can be async (ShouldQueue)                      │
│  ✅ Easy to test                                    │
│  ✅ Can disable/enable easily                       │
│  ✅ Can have multiple listeners                     │
│  ✅ Built-in retry mechanism                        │
│                                                     │
└────────────────────────────────────────────────────┘
```

**Winner: Event/Listener** ✅

---

## 📝 Summary

| Aspect | Implementation | Performance |
|--------|----------------|-------------|
| **Response Time** | Non-blocking async | 50-150ms ⚡ |
| **Anti-Spam** | Cache-based (2 min) | 0-5ms check ✅ |
| **View Tracking** | Background queue | No user impact ✅ |
| **Hot Status** | Auto-updated | Real-time ✅ |
| **Scalability** | Queue + Cache | Highly scalable ✅ |
| **Error Handling** | Retry + Logging | Production-ready ✅ |

**Result: Production-ready, performant, user-friendly system!** 🎉
