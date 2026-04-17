<?php

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';
}

namespace App\Tests\SimpleCache {
    use App\Infrastructure\Cache\SimpleFileCacheAdapter;
    use App\Infrastructure\Bus\Middleware\QueryCacheMiddleware;
    use App\Infrastructure\Bus\Middleware\CommandCacheInvalidationMiddleware;
    use App\Contract\Cqrs\CacheableQueryInterface;
    use App\Contract\Cqrs\QueryInterface;
    use App\Contract\Cqrs\CommandInterface;
    use App\Contract\Cqrs\InvalidatesCacheInterface;
    use App\Infrastructure\Cache\InvalidArgumentException;

    class TestSimpleCacheQuery implements CacheableQueryInterface {
        public function getCacheKey(): string {
            return 'simple_cache_query_key';
        }
        public function getCacheTtl(): int {
            return 3600;
        }
    }

    class TestSimpleCacheCommand implements CommandInterface, InvalidatesCacheInterface {
        public function getCacheKeysToInvalidate(): array {
            return ['simple_cache_query_key', 'another_simple_key'];
        }
    }

    echo "Testing SimpleFileCacheAdapter (PSR-16)...\n";

    $cacheDir = sys_get_temp_dir() . '/psr16_test_cache';
    $cache = new SimpleFileCacheAdapter($cacheDir);
    $cache->clear();

    // Test basic set/get
    $cache->set('test_key', 'test_value', 60);
    if ($cache->get('test_key') !== 'test_value') {
        echo "Simple Cache Failed (set/get)\n";
        exit(1);
    } else {
        echo "Simple Cache set/get OK\n";
    }

    // Test multiple
    $cache->setMultiple(['key1' => 'val1', 'key2' => 'val2'], 60);
    $mult = $cache->getMultiple(['key1', 'key2']);
    if ($mult['key1'] !== 'val1' || $mult['key2'] !== 'val2') {
        echo "Simple Cache Failed (setMultiple/getMultiple)\n";
        exit(1);
    } else {
        echo "Simple Cache setMultiple/getMultiple OK\n";
    }

    // Test multiple with generator
    $generator = function() {
        yield 'gen_key1' => 'gen_val1';
        yield 'gen_key2' => 'gen_val2';
    };

    $cache->setMultiple($generator(), 60);
    $multGen = $cache->getMultiple(['gen_key1', 'gen_key2']);
    if ($multGen['gen_key1'] !== 'gen_val1' || $multGen['gen_key2'] !== 'gen_val2') {
        echo "Simple Cache Failed (setMultiple generator)\n";
        exit(1);
    } else {
        echo "Simple Cache setMultiple generator OK\n";
    }

    $genKeys = function() {
        yield 'gen_key1';
        yield 'gen_key2';
    };
    $cache->deleteMultiple($genKeys());

    if ($cache->has('gen_key1') || $cache->has('gen_key2')) {
        echo "Simple Cache Failed (deleteMultiple generator)\n";
        exit(1);
    } else {
        echo "Simple Cache deleteMultiple generator OK\n";
    }

    // Test invalid argument exceptions
    $invalidExceptionThrown = false;
    try {
        $cache->get('invalid@key');
    } catch (InvalidArgumentException $e) {
        $invalidExceptionThrown = true;
    }
    if (!$invalidExceptionThrown) {
        echo "Simple Cache Failed (invalid key exception)\n";
        exit(1);
    } else {
        echo "Simple Cache invalid key exception OK\n";
    }

    // Test delete
    $cache->delete('test_key');
    if ($cache->has('test_key')) {
        echo "Simple Cache Failed (delete)\n";
        exit(1);
    } else {
        echo "Simple Cache delete OK\n";
    }

    $cache->clear();

    echo "Testing QueryCacheMiddleware with PSR-16...\n";

    $queryMiddleware = new QueryCacheMiddleware($cache);
    $query = new TestSimpleCacheQuery();

    $callCount = 0;
    $next = function (QueryInterface $q) use (&$callCount) {
        $callCount++;
        return 'computed_result_' . $callCount;
    };

    // First call should run next()
    $result1 = $queryMiddleware->handle($query, $next);
    if ($result1 !== 'computed_result_1' || $callCount !== 1) {
        echo "QueryCacheMiddleware (PSR-16) First Run Failed\n";
        exit(1);
    }

    // Second call should return from cache
    $result2 = $queryMiddleware->handle($query, $next);
    if ($result2 !== 'computed_result_1' || $callCount !== 1) {
        echo "QueryCacheMiddleware (PSR-16) Cached Run Failed\n";
        exit(1);
    } else {
        echo "QueryCacheMiddleware (PSR-16) OK\n";
    }

    echo "Testing CommandCacheInvalidationMiddleware with PSR-16...\n";

    $commandMiddleware = new CommandCacheInvalidationMiddleware($cache);
    $command = new TestSimpleCacheCommand();

    $cache->set('another_simple_key', 'some_data', 60);

    $commandNext = function (CommandInterface $c) {
        // do nothing
    };

    $commandMiddleware->handle($command, $commandNext);

    if ($cache->has('simple_cache_query_key') || $cache->has('another_simple_key')) {
        echo "CommandCacheInvalidationMiddleware (PSR-16) Invalidation Failed\n";
        exit(1);
    } else {
        echo "CommandCacheInvalidationMiddleware (PSR-16) OK\n";
    }

    $cache->clear();
    echo "All Simple Cache tests passed!\n";
}
