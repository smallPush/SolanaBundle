<?php

namespace App\Contract\Cqrs;

/**
 * Interface for commands that specify granular cache keys to invalidate upon successful execution.
 */
interface InvalidatesCacheInterface extends CommandInterface
{
    /**
     * Get the list of cache keys to invalidate.
     *
     * @return string[]
     */
    public function getCacheKeysToInvalidate(): array;
}
