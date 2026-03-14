<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Cache\FileCacheAdapter;
use App\Infrastructure\Cache\CacheItem;

$cache = new FileCacheAdapter();
$cache->clear();

$item = $cache->getItem('test_key');
if ($item->isHit()) {
    echo "Error: Empty cache should not be hit\n";
    exit(1);
}

$item->set('test_value');
$item->expiresAfter(2); // 2 seconds
$cache->save($item);

$item2 = $cache->getItem('test_key');
if (!$item2->isHit() || $item2->get() !== 'test_value') {
    echo "Error: Item should be hit and have correct value\n";
    exit(1);
}

sleep(3);

$item3 = $cache->getItem('test_key');
if ($item3->isHit()) {
    echo "Error: Expired item should not be hit\n";
    exit(1);
}

echo "Cache tests passed!\n";

try {
    $cache->getItem('invalid@key');
    echo "Error: Invalid key should throw InvalidArgumentException\n";
    exit(1);
} catch (\App\Infrastructure\Cache\InvalidArgumentException $e) {
    echo "InvalidArgumentException thrown correctly for invalid key.\n";
}

try {
    // Force a CacheException by making the directory inaccessible
    $readOnlyDir = sys_get_temp_dir() . '/readonly_cache_test';
    mkdir($readOnlyDir, 0444); // Read-only
    $badCache = new FileCacheAdapter($readOnlyDir . '/nested');
    echo "Error: Directory creation should have failed and thrown CacheException\n";
    rmdir($readOnlyDir);
    exit(1);
} catch (\App\Infrastructure\Cache\CacheException $e) {
    echo "CacheException thrown correctly for invalid directory.\n";
    rmdir(sys_get_temp_dir() . '/readonly_cache_test');
} catch (\Throwable $t) {
    echo "Unexpected exception for directory failure: " . get_class($t) . "\n";
    @rmdir(sys_get_temp_dir() . '/readonly_cache_test');
}

echo "All cache tests passed successfully.\n";
