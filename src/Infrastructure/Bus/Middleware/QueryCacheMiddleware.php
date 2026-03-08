<?php

namespace App\Infrastructure\Bus\Middleware;

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
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        $result = $next($query);

        $item->set($result);

        $ttl = $query->getCacheTtl();
        $item->expiresAfter($ttl);

        $this->cache->save($item);

        return $result;
    }
}
