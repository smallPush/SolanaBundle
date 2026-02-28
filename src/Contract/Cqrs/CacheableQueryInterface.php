<?php

namespace App\Contract\Cqrs;

/**
 * Interface for queries that can be cached.
 */
interface CacheableQueryInterface extends QueryInterface
{
    /**
     * Returns the cache key for this query.
     */
    public function getCacheKey(): string;

    /**
     * Returns the Time-To-Live for the cache item in seconds.
     * Return null for infinite or default cache duration.
     */
    public function getCacheTtl(): ?int;
}
