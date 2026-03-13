<?php

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';
}

namespace App\Tests\Command {
    use App\Contract\Cqrs\CommandInterface;
    class TestCommand implements CommandInterface {}
}

namespace App\Tests\CommandHandler {
    use App\Contract\Cqrs\CommandHandlerInterface;
    use App\Contract\Cqrs\CommandInterface;

    class TestHandler implements CommandHandlerInterface {
        public bool $handled = false;
        public function __invoke(CommandInterface $command): void {
            $this->handled = true;
        }
    }
}

namespace App\Tests\Query {
    use App\Contract\Cqrs\QueryInterface;
    class TestQuery implements QueryInterface {}

    use App\Contract\Cqrs\CacheableQueryInterface;
    class TestCacheableQuery implements CacheableQueryInterface {
        public function getCacheKey(): string {
            return 'test_cache_key';
        }
        public function getCacheTtl(): int {
            return 3600;
        }
    }
}

namespace App\Tests\QueryHandler {
    use App\Contract\Cqrs\QueryHandlerInterface;
    use App\Contract\Cqrs\QueryInterface;
    use App\Tests\Query\TestCacheableQuery;

    class TestHandler implements QueryHandlerInterface {
        public function __invoke(QueryInterface $query): mixed {
            return 'result';
        }
    }

    class TestCacheableHandler implements QueryHandlerInterface {
        public int $callCount = 0;
        public function __invoke(QueryInterface $query): mixed {
            $this->callCount++;
            return 'cached_result_' . $this->callCount;
        }
    }
}

namespace App\Tests\CommandMiddleware {
    use App\Contract\Bus\CommandMiddlewareInterface;
    use App\Contract\Cqrs\CommandInterface;

    class TestCommandMiddleware implements CommandMiddlewareInterface {
        public bool $called = false;
        public function handle(CommandInterface $command, callable $next): void {
            $this->called = true;
            $next($command);
        }
    }
}

namespace App\Tests {
    use App\Infrastructure\Bus\SimpleCommandBus;
    use App\Tests\CommandMiddleware\TestCommandMiddleware;
    use App\Infrastructure\Bus\SimpleQueryBus;
    use App\Infrastructure\Bus\Middleware\QueryCacheMiddleware;
    use App\Infrastructure\Bus\Middleware\CommandCacheInvalidationMiddleware;
    use App\Infrastructure\Cache\FileCacheAdapter;
    use App\Tests\Command\TestCommand;
    use App\Tests\CommandHandler\TestHandler as CmdHandler;
    use App\Tests\Query\TestQuery;
    use App\Tests\Query\TestCacheableQuery;
    use App\Tests\QueryHandler\TestHandler as QryHandler;
    use App\Tests\QueryHandler\TestCacheableHandler as QryCacheableHandler;
    use Psr\Container\ContainerInterface;

    class SimpleContainer implements ContainerInterface {
        private array $services = [];
        public function set(string $id, object $service): void { $this->services[$id] = $service; }
        public function get(string $id) {
            if(!isset($this->services[$id])) throw new \Exception("Service $id not found");
            return $this->services[$id];
        }
        public function has(string $id): bool { return isset($this->services[$id]); }
    }

    echo "Testing Namespace Logic...\n";

    use App\Infrastructure\Bus\Locator\ConventionBasedHandlerLocator;

    $container = new SimpleContainer();
    $locator = new ConventionBasedHandlerLocator($container);

    // Command
    // App\Tests\Command\TestCommand -> App\Tests\CommandHandler\TestHandler
    $cmdHandler = new CmdHandler();
    $container->set(CmdHandler::class, $cmdHandler);

    $cacheDir = sys_get_temp_dir() . '/cqrs_test_cache';
    $cacheAdapter = new FileCacheAdapter($cacheDir);
    $cacheAdapter->clear();

    $invalidationMiddleware = new CommandCacheInvalidationMiddleware($cacheAdapter);
    $middleware = new TestCommandMiddleware();

    // Add an item to cache to test invalidation
    $item = $cacheAdapter->getItem('test_command_cache');
    $item->set('some_data');
    $cacheAdapter->save($item);

    $bus = new SimpleCommandBus($locator, [$middleware, $invalidationMiddleware]);
    try {
        $bus->dispatch(new TestCommand());
        if ($cmdHandler->handled && $middleware->called) {
            echo "Command OK (with Middleware)\n";

            // Check if cache was cleared
            if ($cacheAdapter->getItem('test_command_cache')->isHit()) {
                echo "Command Failed (Cache not invalidated)\n";
                exit(1);
            } else {
                echo "Command Cache Invalidation OK\n";
            }
        } else {
            echo "Command Failed (Not Handled or Middleware skipped)\n";
            exit(1);
        }
    } catch (\Throwable $e) {
        echo "Command Error: " . $e->getMessage() . "\n";
        exit(1);
    }

    // Query
    // App\Tests\Query\TestQuery -> App\Tests\QueryHandler\TestHandler
    $qryHandler = new QryHandler();
    $container->set(QryHandler::class, $qryHandler);

    $qBus = new SimpleQueryBus($locator);
    try {
        $res = $qBus->ask(new TestQuery());
        if ($res === 'result') {
            echo "Query OK\n";
        } else {
            echo "Query Failed (Wrong Result)\n";
            exit(1);
        }
    } catch (\Throwable $e) {
        echo "Query Error: " . $e->getMessage() . "\n";
        exit(1);
    }

    echo "Testing Cache Middleware...\n";

    $cacheAdapter->clear();

    $cacheMiddleware = new QueryCacheMiddleware($cacheAdapter);

    $cacheableQryHandler = new QryCacheableHandler();
    $container->set(QryCacheableHandler::class, $cacheableQryHandler);

    $qBusWithCache = new SimpleQueryBus($locator, [$cacheMiddleware]);

    try {
        $query1 = new TestCacheableQuery();
        $res1 = $qBusWithCache->ask($query1);

        if ($res1 === 'cached_result_1' && $cacheableQryHandler->callCount === 1) {
            echo "Cache Middleware Query (First run) OK\n";
        } else {
            echo "Cache Middleware Query (First run) Failed\n";
            exit(1);
        }

        $query2 = new TestCacheableQuery();
        $res2 = $qBusWithCache->ask($query2);

        if ($res2 === 'cached_result_1' && $cacheableQryHandler->callCount === 1) {
            echo "Cache Middleware Query (Cached run) OK\n";
        } else {
            echo "Cache Middleware Query (Cached run) Failed\n";
            exit(1);
        }
    } catch (\Throwable $e) {
        echo "Cache Middleware Error: " . $e->getMessage() . "\n";
        exit(1);
    } finally {
        $cacheAdapter->clear();
    }

    echo "All tests passed!\n";
}
