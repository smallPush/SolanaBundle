<?php

namespace App\Infrastructure\Bus;

use App\Contract\Bus\QueryMiddlewareInterface;
use App\Contract\Cqrs\CacheableQueryInterface;
use App\Contract\Cqrs\QueryInterface;
use Psr\Cache\CacheItemPoolInterface;

class QueryCacheMiddleware implements QueryMiddlewareInterface
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function handle(QueryInterface $query, callable $next): mixed
    {
        if (!$query instanceof CacheableQueryInterface) {
            return $next($query);
        }

        $key = $query->getCacheKey();
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = $next($query);

        $cacheItem->set($result);
        $cacheItem->expiresAfter($query->getCacheTtl());
        $this->cache->save($cacheItem);

        return $result;
    }
}
