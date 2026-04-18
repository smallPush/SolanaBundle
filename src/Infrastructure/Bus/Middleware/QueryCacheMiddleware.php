<?php

namespace App\Infrastructure\Bus\Middleware;

use App\Contract\Bus\QueryMiddlewareInterface;
use App\Contract\Cqrs\CacheableQueryInterface;
use App\Contract\Cqrs\QueryInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

class QueryCacheMiddleware implements QueryMiddlewareInterface
{
    private CacheItemPoolInterface|CacheInterface $cache;

    public function __construct(CacheItemPoolInterface|CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function handle(QueryInterface $query, callable $next): mixed
    {
        if (!$query instanceof CacheableQueryInterface) {
            return $next($query);
        }

        $key = $query->getCacheKey();
        $ttl = $query->getCacheTtl();

        if ($this->cache instanceof CacheInterface) {
            // PSR-16
            if ($this->cache->has($key)) {
                return $this->cache->get($key);
            }

            $result = $next($query);
            $this->cache->set($key, $result, $ttl);
            return $result;
        }

        // PSR-6
        $item = $this->cache->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }

        $result = $next($query);

        $item->set($result);
        $item->expiresAfter($ttl);

        $this->cache->save($item);

        return $result;
    }
}
