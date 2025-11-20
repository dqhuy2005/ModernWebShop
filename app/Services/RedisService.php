<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Predis\Connection\ConnectionException;

class RedisService
{
    private static ?bool $isConnected = null;
    private static ?float $lastCheckTime = null;

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

    public function resetConnectionState(): void
    {
        self::$isConnected = null;
        self::$lastCheckTime = null;
    }

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

    public function putMany(array $values, ?int $ttl = null): bool
    {
        try {
            $serialized = [];

            foreach ($values as $key => $value) {
                $serialized[$key] = $this->serialize($value);
            }

            $result = Redis::mset($serialized);

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
}
