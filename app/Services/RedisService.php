<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Predis\Connection\ConnectionException;

class RedisService
{
    private static ?bool $isConnected = null;
    private static ?float $lastCheckTime = null;
    private const CHECK_INTERVAL = 5.0; // seconds

    // Cache monitoring metrics
    private static array $metrics = [
        'hits' => 0,
        'misses' => 0,
        'queries' => 0,
        'total_time' => 0.0,
    ];

    protected function isRedisAvailable(): bool
    {
        $now = microtime(true);

        if (self::$lastCheckTime !== null && ($now - self::$lastCheckTime) < self::CHECK_INTERVAL) {
            return self::$isConnected ?? false;
        }

        try {
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

    public function get(string $key, $default = null)
    {
        $startTime = microtime(true);
        self::$metrics['queries']++;

        if (!$this->isRedisAvailable()) {
            self::$metrics['misses']++;
            return $default;
        }

        try {
            $value = Redis::get($key);

            if ($value === null) {
                self::$metrics['misses']++;
                self::$metrics['total_time'] += (microtime(true) - $startTime);
                return $default;
            }

            self::$metrics['hits']++;
            self::$metrics['total_time'] += (microtime(true) - $startTime);

            return $this->unserialize($value);
        } catch (ConnectionException $e) {
            self::$isConnected = false;
            self::$metrics['misses']++;
            Log::warning('RedisService: Connection lost during get', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        } catch (\Exception $e) {
            self::$metrics['misses']++;
            Log::error('RedisService: Error getting key', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return $default;
        }
    }

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

    public function remember(string $key, int $ttl, callable $callback)
    {
        if (!$this->isRedisAvailable()) {
            Log::debug('RedisService: Redis unavailable, executing callback directly', ['key' => $key]);
            return $callback();
        }

        try {
            $cached = $this->get($key);

            if ($cached !== null) {
                Log::debug('RedisService: Cache HIT', ['key' => $key]);
                return $cached;
            }

            Log::debug('RedisService: Cache MISS', ['key' => $key]);
            $value = $callback();

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

            return $callback();
        }
    }

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

    public function ping(): bool
    {
        try {
            $response = Redis::connection()->ping();

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

    protected function serialize($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        return serialize($value);
    }

    protected function unserialize(string $value)
    {
        $unserialized = @unserialize($value);

        return $unserialized !== false ? $unserialized : $value;
    }

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

    protected function toBool($response): bool
    {
        if (is_object($response) && method_exists($response, 'getPayload')) {
            $payload = $response->getPayload();
            return $payload === 'OK' || $payload === 'PONG' || $payload === true;
        }

        if (is_bool($response)) {
            return $response;
        }

        if (is_int($response)) {
            return $response > 0;
        }

        return (bool) $response;
    }

    public function getMetrics(): array
    {
        $hitRate = 0.0;
        $avgQueryTime = 0.0;

        if (self::$metrics['queries'] > 0) {
            $total = self::$metrics['hits'] + self::$metrics['misses'];
            $hitRate = $total > 0 ? round((self::$metrics['hits'] / $total) * 100, 2) : 0.0;
            $avgQueryTime = round((self::$metrics['total_time'] / self::$metrics['queries']) * 1000, 2);
        }

        return [
            'hits' => self::$metrics['hits'],
            'misses' => self::$metrics['misses'],
            'queries' => self::$metrics['queries'],
            'hit_rate' => $hitRate,
            'avg_query_time_ms' => $avgQueryTime,
            'total_time_ms' => round(self::$metrics['total_time'] * 1000, 2),
        ];
    }

    public function rememberWithWarming(string $key, int $ttl, callable $callback, int $refreshBeforeSeconds = 300)
    {
        if (!$this->isRedisAvailable()) {
            return $callback();
        }

        try {
            $value = $this->get($key);
            $remainingTtl = $this->ttl($key);

            if ($value !== null && $remainingTtl > $refreshBeforeSeconds) {
                return $value;
            }

            $newValue = $callback();
            $this->set($key, $newValue, $ttl);

            if ($value !== null && $remainingTtl > 0) {
                return $value;
            }

            return $newValue;
        } catch (\Exception $e) {
            Log::error('RedisService: Cache warming failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $callback();
        }
    }

    public function getCacheStats(): array
    {
        if (!$this->isRedisAvailable()) {
            return [
                'status' => 'unavailable',
                'message' => 'Redis is not available'
            ];
        }

        try {
            $info = Redis::info();
            $metrics = $this->getMetrics();

            return [
                'status' => 'online',
                'server' => [
                    'version' => $info['redis_version'] ?? 'unknown',
                    'uptime_days' => isset($info['uptime_in_seconds']) ? round($info['uptime_in_seconds'] / 86400, 2) : 0,
                    'connected_clients' => $info['connected_clients'] ?? 0,
                ],
                'memory' => [
                    'used_memory_human' => $info['used_memory_human'] ?? '0',
                    'used_memory_peak_human' => $info['used_memory_peak_human'] ?? '0',
                    'mem_fragmentation_ratio' => $info['mem_fragmentation_ratio'] ?? 0,
                ],
                'stats' => [
                    'total_commands_processed' => $info['total_commands_processed'] ?? 0,
                    'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                    'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                    'keyspace_hit_rate' => $this->calculateHitRate($info),
                ],
                'application_metrics' => $metrics,
            ];
        } catch (\Exception $e) {
            Log::error('RedisService: Failed to get cache stats', [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
