<?php

namespace App\Services;

interface IRedisService
{
    public function get(string $key);

    public function set(string $key, $value, ?int $ttl = null): bool;

    public function remember(string $key, int $ttl, callable $callback);

    public function forget(string|array $keys): bool;

    public function flush(): bool;

    public function deleteByPattern(string $pattern): int;

    public function exists(string $key): bool;

    public function ping(): bool;

    public function getMetrics(): array;
}
