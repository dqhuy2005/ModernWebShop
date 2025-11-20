<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Predis\Connection\ConnectionException;

/**
 * Redis Service
 *
 * Centralized Redis operations handler
 * Provides reusable methods for Redis caching with error handling
 * Supports: get, set, delete, exists, increment, decrement, tags, TTL management
 */
class RedisService
{
    /**
     * Redis connection state
     * Prevents repeated connection attempts when Redis is down
     */
    private static ?bool $isConnected = null;
    private static ?float $lastCheckTime = null;
    private const CHECK_INTERVAL = 5.0; // seconds

    /**
     * Check if Redis is available
     * Uses cached state to avoid repeated connection attempts
     *
     * @return bool
     */
    protected function isRedisAvailable(): bool
    {
        $now = microtime(true);

        // Use cached state if checked recently
        if (self::$lastCheckTime !== null && ($now - self::$lastCheckTime) < self::CHECK_INTERVAL) {
            return self::$isConnected ?? false;
        }

        try {
            // Quick ping with timeout
            $result = Redis::ping();
            self::$isConnected = true;
            self::$lastCheckTime = $now;
            return true;
        } catch (ConnectionException $e) {
            self::$isConnected = false;
            self::$lastCheckTime = $now;
            Log::warning('RedisService: Connection unavailable', [
                'error' => $e->getMessage()
            ]);
            return false;
        } catch (\Exception $e) {
            self::$isConnected = false;
            self::$lastCheckTime = $now;
            Log::warning('RedisService: Redis check failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Reset connection state (useful after Redis restart)
     *
     * @return void
     */
    public function resetConnectionState(): void
    {
        self::$isConnected = null;
        self::$lastCheckTime = null;
    }
    /**
     * Get value from Redis cache
     *
     * @param string $key Cache key
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (!$this->isRedisAvailable()) {
            return $default;
        }

        try {
            $value = Redis::get($key);

            if ($value === null) {
                return $default;
            }

            // Deserialize if it's a serialized object
            return $this->unserialize($value);
        } catch (ConnectionException $e) {
            self::$isConnected = false;
            Log::warning('RedisService: Connection lost during get', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        } catch (\Exception $e) {
            Log::error('RedisService: Error getting key', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return $default;
        }
    }

    /**
     * Set value in Redis cache
     *
     * @param string $key Cache key
     * @param mixed $value Value to cache
     * @param int|null $ttl Time to live in seconds (null = no expiration)
     * @return bool
     */
    public function set(string $key, $value, ?int $ttl = null): bool
    {
        if (!$this->isRedisAvailable()) {
            return false;
        }

        try {
            $serialized = $this->serialize($value);

            if ($ttl === null) {
                $result = Redis::set($key, $serialized);
                return $this->toBool($result);
            }

            $result = Redis::setex($key, $ttl, $serialized);
            return $this->toBool($result);
        } catch (ConnectionException $e) {
            self::$isConnected = false;
            Log::warning('RedisService: Connection lost during set', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('RedisService: Error setting key', [
                'key' => $key,
                'ttl' => $ttl,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Remember pattern: Get from cache or execute callback and cache result
     *
     * @param string $key Cache key
     * @param int $ttl Time to live in seconds
     * @param callable $callback Function to execute if cache miss
     * @return mixed
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        // If Redis is unavailable, execute callback directly
        if (!$this->isRedisAvailable()) {
            Log::debug('RedisService: Redis unavailable, executing callback directly', ['key' => $key]);
            return $callback();
        }

        try {
            // Try to get from cache
            $cached = $this->get($key);

            if ($cached !== null) {
                Log::debug('RedisService: Cache HIT', ['key' => $key]);
                return $cached;
            }

            // Cache miss - execute callback
            Log::debug('RedisService: Cache MISS', ['key' => $key]);
            $value = $callback();

            // Store in cache
            $this->set($key, $value, $ttl);

            return $value;
        } catch (ConnectionException $e) {
            self::$isConnected = false;
            Log::warning('RedisService: Connection lost, falling back to callback', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $callback();
        } catch (\Exception $e) {
            Log::error('RedisService: Remember pattern failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            // Fallback: execute callback without caching
            return $callback();
        }
    }

    /**
     * Delete key(s) from Redis
     *
     * @param string|array $keys Single key or array of keys
     * @return int Number of keys deleted
     */
    public function forget($keys): int
    {
        if (!$this->isRedisAvailable()) {
            return 0;
        }

        try {
            if (is_array($keys)) {
                return Redis::del(...$keys);
            }

            return Redis::del($keys);
        } catch (ConnectionException $e) {
            self::$isConnected = false;
            Log::warning('RedisService: Connection lost during forget', [
                'keys' => $keys,
                'error' => $e->getMessage()
            ]);
            return 0;
        } catch (\Exception $e) {
            Log::error('RedisService: Error deleting keys', [
                'keys' => $keys,
                'error' => $e->getMessage()
            ]);

            return 0;
        }
    }

    /**
     * Check if key exists in Redis
     *
     * @param string $key Cache key
     * @return bool
     */
    public function has(string $key): bool
    {
        if (!$this->isRedisAvailable()) {
            return false;
        }

        try {
            return (bool) Redis::exists($key);
        } catch (ConnectionException $e) {
            self::$isConnected = false;
            Log::warning('RedisService: Connection lost during has', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('RedisService: Error checking key existence', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get multiple keys at once
     *
     * @param array $keys Array of cache keys
     * @return array Associative array of key => value
     */
    public function many(array $keys): array
    {
        try {
            $values = Redis::mget($keys);
            $result = [];

            foreach ($keys as $index => $key) {
                $result[$key] = $values[$index] !== false
                    ? $this->unserialize($values[$index])
                    : null;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('RedisService: Error getting multiple keys', [
                'keys' => $keys,
                'error' => $e->getMessage()
            ]);

            return array_fill_keys($keys, null);
        }
    }

    /**
     * Set multiple key-value pairs at once
     *
     * @param array $values Associative array of key => value
     * @param int|null $ttl Time to live in seconds
     * @return bool
     */
    public function putMany(array $values, ?int $ttl = null): bool
    {
        try {
            $serialized = [];

            foreach ($values as $key => $value) {
                $serialized[$key] = $this->serialize($value);
            }

            $result = Redis::mset($serialized);

            // Set TTL for each key if specified
            if ($ttl !== null && $this->toBool($result)) {
                foreach (array_keys($serialized) as $key) {
                    Redis::expire($key, $ttl);
                }
            }

            return $this->toBool($result);
        } catch (\Exception $e) {
            Log::error('RedisService: Error setting multiple keys', [
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Increment a numeric value
     *
     * @param string $key Cache key
     * @param int $value Increment by this value (default: 1)
     * @return int New value after increment
     */
    public function increment(string $key, int $value = 1): int
    {
        try {
            return Redis::incrby($key, $value);
        } catch (\Exception $e) {
            Log::error('RedisService: Error incrementing key', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return 0;
        }
    }

    /**
     * Decrement a numeric value
     *
     * @param string $key Cache key
     * @param int $value Decrement by this value (default: 1)
     * @return int New value after decrement
     */
    public function decrement(string $key, int $value = 1): int
    {
        try {
            return Redis::decrby($key, $value);
        } catch (\Exception $e) {
            Log::error('RedisService: Error decrementing key', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return 0;
        }
    }

    /**
     * Get remaining TTL for a key
     *
     * @param string $key Cache key
     * @return int TTL in seconds, -1 if no TTL, -2 if key doesn't exist
     */
    public function ttl(string $key): int
    {
        try {
            return Redis::ttl($key);
        } catch (\Exception $e) {
            Log::error('RedisService: Error getting TTL', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return -2;
        }
    }

    /**
     * Update TTL for existing key
     *
     * @param string $key Cache key
     * @param int $ttl New TTL in seconds
     * @return bool
     */
    public function expire(string $key, int $ttl): bool
    {
        try {
            $result = Redis::expire($key, $ttl);
            return $this->toBool($result);
        } catch (\Exception $e) {
            Log::error('RedisService: Error setting expiration', [
                'key' => $key,
                'ttl' => $ttl,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Remove TTL from key (make it persistent)
     *
     * @param string $key Cache key
     * @return bool
     */
    public function persist(string $key): bool
    {
        try {
            $result = Redis::persist($key);
            return $this->toBool($result);
        } catch (\Exception $e) {
            Log::error('RedisService: Error persisting key', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Delete keys matching a pattern
     *
     * @param string $pattern Pattern to match (e.g., "homepage:*")
     * @return int Number of keys deleted
     */
    public function deleteByPattern(string $pattern): int
    {
        try {
            $keys = Redis::keys($pattern);

            if (empty($keys)) {
                return 0;
            }

            return $this->forget($keys);
        } catch (\Exception $e) {
            Log::error('RedisService: Error deleting by pattern', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);

            return 0;
        }
    }

    /**
     * Flush entire Redis database (USE WITH CAUTION)
     *
     * @return bool
     */
    public function flush(): bool
    {
        try {
            Redis::flushdb();

            Log::warning('RedisService: Entire Redis database flushed');

            return true;
        } catch (\Exception $e) {
            Log::error('RedisService: Error flushing database', [
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get all keys matching a pattern
     *
     * @param string $pattern Pattern to match (e.g., "homepage:*")
     * @return array Array of matching keys
     */
    public function keys(string $pattern = '*'): array
    {
        try {
            return Redis::keys($pattern) ?: [];
        } catch (\Exception $e) {
            Log::error('RedisService: Error getting keys', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Get cache statistics
     *
     * @return array
     */
    public function stats(): array
    {
        try {
            $info = Redis::info();

            return [
                'redis_version' => $info['redis_version'] ?? 'unknown',
                'used_memory_human' => $info['used_memory_human'] ?? 'unknown',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'total_commands_processed' => $info['total_commands_processed'] ?? 0,
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $this->calculateHitRate($info),
            ];
        } catch (\Exception $e) {
            Log::error('RedisService: Error getting stats', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Ping Redis server
     *
     * @return bool True if Redis is responsive
     */
    public function ping(): bool
    {
        try {
            $response = Redis::connection()->ping();

            // Handle different response types (predis returns Status object, phpredis returns string)
            if (is_object($response) && method_exists($response, 'getPayload')) {
                $isConnected = $response->getPayload() === 'PONG';
                self::$isConnected = $isConnected;
                self::$lastCheckTime = microtime(true);
                return $isConnected;
            }

            $isConnected = $response === 'PONG' || $response === true;
            self::$isConnected = $isConnected;
            self::$lastCheckTime = microtime(true);
            return $isConnected;
        } catch (ConnectionException $e) {
            self::$isConnected = false;
            self::$lastCheckTime = microtime(true);
            Log::warning('RedisService: Redis connection failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        } catch (\Exception $e) {
            self::$isConnected = false;
            self::$lastCheckTime = microtime(true);
            Log::error('RedisService: Redis ping failed', [
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Serialize value for storage
     *
     * @param mixed $value
     * @return string
     */
    protected function serialize($value): string
    {
        // Don't serialize strings, keep them readable in Redis
        if (is_string($value)) {
            return $value;
        }

        // Serialize complex types
        return serialize($value);
    }

    /**
     * Unserialize value from storage
     *
     * @param string $value
     * @return mixed
     */
    protected function unserialize(string $value)
    {
        // Try to unserialize
        $unserialized = @unserialize($value);

        // If unserialize fails, return original string
        return $unserialized !== false ? $unserialized : $value;
    }

    /**
     * Calculate cache hit rate
     *
     * @param array $info Redis info array
     * @return float Hit rate percentage
     */
    protected function calculateHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;

        if ($total === 0) {
            return 0.0;
        }

        return round(($hits / $total) * 100, 2);
    }

    /**
     * Convert predis response to boolean
     * Handles Predis\Response\Status objects and other response types
     *
     * @param mixed $response
     * @return bool
     */
    protected function toBool($response): bool
    {
        // Handle predis Status objects
        if (is_object($response) && method_exists($response, 'getPayload')) {
            $payload = $response->getPayload();
            return $payload === 'OK' || $payload === 'PONG' || $payload === true;
        }

        // Handle direct boolean or integer responses
        if (is_bool($response)) {
            return $response;
        }

        if (is_int($response)) {
            return $response > 0;
        }

        return (bool) $response;
    }
}
