<?php

namespace App\Infrastructure\Bus\Middleware;

use App\Contract\Bus\CommandMiddlewareInterface;
use App\Contract\Cqrs\CommandInterface;
use Psr\Cache\CacheItemPoolInterface;

class CommandCacheInvalidationMiddleware implements CommandMiddlewareInterface
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function handle(CommandInterface $command, callable $next): void
    {
        // Execute the command first
        $next($command);

        // If the command completes without throwing an exception, clear the cache
        // In a purely academic implementation without granular tagging, we clear the entire query cache
        // to ensure any read models reflect the latest state.
        $this->cache->clear();
    }
}
