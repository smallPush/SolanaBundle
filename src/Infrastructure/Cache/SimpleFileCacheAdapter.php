<?php

namespace App\Infrastructure\Cache;

use Psr\SimpleCache\CacheInterface;

class SimpleFileCacheAdapter implements CacheInterface
{
    private string $cacheDir;

    public function __construct(string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?? sys_get_temp_dir() . '/app_simple_cache';
        if (!is_dir($this->cacheDir)) {
            if (!@mkdir($this->cacheDir, 0777, true) && !is_dir($this->cacheDir)) {
                throw new InvalidArgumentException(sprintf('Directory "%s" was not created', $this->cacheDir));
            }
        }
    }

    private function validateKey(mixed $key): void
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Key must be a string.');
        }

        if (!preg_match('/^[a-zA-Z0-9_.]{1,64}$/', $key)) {
            throw new InvalidArgumentException(sprintf('Invalid key: "%s".', $key));
        }
    }

    private function getFilename(string $key): string
    {
        return $this->cacheDir . '/' . md5($key);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);
        $file = $this->getFilename($key);

        if (file_exists($file)) {
            $data = @unserialize(@file_get_contents($file));
            if ($data !== false && is_array($data) && array_key_exists('expiresAt', $data) && array_key_exists('value', $data)) {
                if ($data['expiresAt'] === null || $data['expiresAt'] > new \DateTimeImmutable()) {
                    return $data['value'];
                } else {
                    @unlink($file); // Expired
                }
            } else {
                @unlink($file); // Invalid data
            }
        }

        return $default;
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $this->validateKey($key);
        $file = $this->getFilename($key);

        $expiresAt = null;
        if (is_int($ttl)) {
            $expiresAt = new \DateTimeImmutable('+' . $ttl . ' seconds');
        } elseif ($ttl instanceof \DateInterval) {
            $expiresAt = (new \DateTimeImmutable())->add($ttl);
        }

        $data = [
            'value' => $value,
            'expiresAt' => $expiresAt,
        ];

        $tmpFile = $file . uniqid('.tmp', true);
        if (@file_put_contents($tmpFile, serialize($data)) === false) {
            return false;
        }

        return @rename($tmpFile, $file);
    }

    public function delete(string $key): bool
    {
        $this->validateKey($key);
        $file = $this->getFilename($key);
        if (file_exists($file)) {
            return @unlink($file);
        }
        return true;
    }

    public function clear(): bool
    {
        $files = @glob($this->cacheDir . '/*');
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $this->validateKey($key);
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        $success = true;
        foreach ($values as $key => $value) {
            $this->validateKey($key);
            if (!$this->set($key, $value, $ttl)) {
                $success = false;
            }
        }
        return $success;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $success = true;
        foreach ($keys as $key) {
            $this->validateKey($key);
            if (!$this->delete($key)) {
                $success = false;
            }
        }
        return $success;
    }

    public function has(string $key): bool
    {
        $this->validateKey($key);
        $file = $this->getFilename($key);

        if (file_exists($file)) {
            $data = @unserialize(@file_get_contents($file));
            if ($data !== false && is_array($data) && array_key_exists('expiresAt', $data) && array_key_exists('value', $data)) {
                if ($data['expiresAt'] === null || $data['expiresAt'] > new \DateTimeImmutable()) {
                    return true;
                } else {
                    @unlink($file); // Expired
                }
            } else {
                @unlink($file); // Invalid data
            }
        }

        return false;
    }
}
