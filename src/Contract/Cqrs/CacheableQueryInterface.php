<?php

namespace App\Contract\Cqrs;

interface CacheableQueryInterface extends QueryInterface
{
    /**
     * Get the cache key for the query.
     */
    public function getCacheKey(): string;

    /**
     * Get the cache Time-To-Live (TTL) in seconds.
     */
    public function getCacheTtl(): int;
}
