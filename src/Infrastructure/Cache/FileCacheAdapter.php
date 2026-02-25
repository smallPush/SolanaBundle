<?php

namespace App\Infrastructure\Cache;

use App\Contract\Psr\Cache\CacheItemInterface;
use App\Contract\Psr\Cache\CacheItemPoolInterface;

class FileCacheAdapter implements CacheItemPoolInterface
{
    private string $cacheDir;
    private array $deferred = [];

    public function __construct(string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?? sys_get_temp_dir() . '/app_cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function getItem(string $key): CacheItemInterface
    {
        $file = $this->getFilename($key);
        if (file_exists($file)) {
            $data = unserialize(file_get_contents($file));
            if ($data['expiresAt'] === null || $data['expiresAt'] > new \DateTimeImmutable()) {
                return new CacheItem($key, $data['value'], true);
            } else {
                unlink($file); // Expired
            }
        }

        return new CacheItem($key, null, false);
    }

    public function getItems(array $keys = []): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->getItem($key);
        }
    }

    public function hasItem(string $key): bool
    {
        return $this->getItem($key)->isHit();
    }

    public function clear(): bool
    {
        $files = glob($this->cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }

    public function deleteItem(string $key): bool
    {
        $file = $this->getFilename($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return true;
    }

    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }
        return true;
    }

    public function save(CacheItemInterface $item): bool
    {
        $file = $this->getFilename($item->getKey());

        // We need to access expiration, which is not in the interface, so we cast or rely on implementation detail
        // For academic purpose, assume implementation detail
        $expiration = null;
        if ($item instanceof CacheItem) {
             $expiration = $item->getExpiration();
        }

        $data = [
            'value' => $item->get(),
            'expiresAt' => $expiration,
        ];

        return (bool) file_put_contents($file, serialize($data));
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[] = $item;
        return true;
    }

    public function commit(): bool
    {
        foreach ($this->deferred as $item) {
            $this->save($item);
        }
        $this->deferred = [];
        return true;
    }

    private function getFilename(string $key): string
    {
        return $this->cacheDir . '/' . md5($key);
    }
}
