<?php

namespace App\Contract\Psr\Cache;

/**
 * CacheItemPoolInterface generates CacheItemInterface objects.
 */
interface CacheItemPoolInterface
{
    /**
     * Returns a Cache Item representing the specified key.
     *
     * @param string $key
     *
     * @return CacheItemInterface
     */
    public function getItem(string $key): CacheItemInterface;

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *
     * @return iterable
     */
    public function getItems(array $keys = []): iterable;

    /**
     * Confirms if the cache contains specified cache item.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasItem(string $key): bool;

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     */
    public function clear(): bool;

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *
     * @return bool
     */
    public function deleteItem(string $key): bool;

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys
     *
     * @return bool
     */
    public function deleteItems(array $keys): bool;

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *
     * @return bool
     */
    public function save(CacheItemInterface $item): bool;

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *
     * @return bool
     */
    public function saveDeferred(CacheItemInterface $item): bool;

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     */
    public function commit(): bool;
}
