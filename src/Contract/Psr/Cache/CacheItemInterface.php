<?php

namespace App\Contract\Psr\Cache;

/**
 * CacheItemInterface defines an interface for interacting with objects inside a cache.
 */
interface CacheItemInterface
{
    /**
     * Returns the key for the current cache item.
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * @return mixed
     */
    public function get(): mixed;

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * @return bool
     */
    public function isHit(): bool;

    /**
     * Sets the value represented by this cache item.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function set(mixed $value): static;

    /**
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface|null $expiration
     *
     * @return static
     */
    public function expiresAt(?\DateTimeInterface $expiration): static;

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval|null $time
     *
     * @return static
     */
    public function expiresAfter(int|\DateInterval|null $time): static;
}
