<?php

namespace App\Infrastructure\Cache;

use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    private string $key;
    private mixed $value;
    private bool $isHit;
    private ?\DateTimeInterface $expiration;

    public function __construct(string $key, mixed $value, bool $isHit)
    {
        $this->key = $key;
        $this->value = $value;
        $this->isHit = $isHit;
        $this->expiration = null;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->expiration = $expiration;
        return $this;
    }

    public function expiresAfter(int|\DateInterval|null $time): static
    {
        if (is_int($time)) {
            $this->expiration = new \DateTimeImmutable('+' . $time . ' seconds');
        } elseif ($time instanceof \DateInterval) {
            $this->expiration = (new \DateTimeImmutable())->add($time);
        } else {
            $this->expiration = null;
        }

        return $this;
    }

    public function getExpiration(): ?\DateTimeInterface
    {
        return $this->expiration;
    }
}
