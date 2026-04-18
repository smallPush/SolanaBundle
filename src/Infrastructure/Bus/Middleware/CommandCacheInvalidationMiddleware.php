<?php

namespace App\Infrastructure\Bus\Middleware;

use App\Contract\Bus\CommandMiddlewareInterface;
use App\Contract\Cqrs\CommandInterface;
use App\Contract\Cqrs\InvalidatesCacheInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

class CommandCacheInvalidationMiddleware implements CommandMiddlewareInterface
{
    private CacheItemPoolInterface|CacheInterface $cache;

    public function __construct(CacheItemPoolInterface|CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function handle(CommandInterface $command, callable $next): void
    {
        // Execute the command first
        $next($command);

        // If the command completes without throwing an exception, clear or invalidate cache
        if ($command instanceof InvalidatesCacheInterface) {
            $keys = $command->getCacheKeysToInvalidate();
            if (!empty($keys)) {
                if ($this->cache instanceof CacheInterface) {
                    $this->cache->deleteMultiple($keys);
                } else {
                    $this->cache->deleteItems($keys);
                }
            }
        } else {
            // Fallback for commands not implementing granular invalidation
            $this->cache->clear();
        }
    }
}
