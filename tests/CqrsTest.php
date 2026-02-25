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
}

namespace App\Tests\QueryHandler {
    use App\Contract\Cqrs\QueryHandlerInterface;
    use App\Contract\Cqrs\QueryInterface;

    class TestHandler implements QueryHandlerInterface {
        public function __invoke(QueryInterface $query): mixed {
            return 'result';
        }
    }
}

namespace App\Tests {
    use App\Infrastructure\Bus\SimpleCommandBus;
    use App\Infrastructure\Bus\SimpleQueryBus;
    use App\Tests\Command\TestCommand;
    use App\Tests\CommandHandler\TestHandler as CmdHandler;
    use App\Tests\Query\TestQuery;
    use App\Tests\QueryHandler\TestHandler as QryHandler;
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

    $container = new SimpleContainer();

    // Command
    // App\Tests\Command\TestCommand -> App\Tests\CommandHandler\TestHandler
    $cmdHandler = new CmdHandler();
    $container->set(CmdHandler::class, $cmdHandler);

    $bus = new SimpleCommandBus($container);
    try {
        $bus->dispatch(new TestCommand());
        if ($cmdHandler->handled) {
            echo "Command OK\n";
        } else {
            echo "Command Failed (Not Handled)\n";
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

    $qBus = new SimpleQueryBus($container);
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

    echo "All tests passed!\n";
}
