<?php

namespace App\Tests;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/stubs.php';

use App\Application\Query\GetSolanaContractByIdQuery;
use App\Application\QueryHandler\GetSolanaContractByIdHandler;
use App\Contract\Cqrs\QueryHandlerInterface;
use App\Contract\Cqrs\QueryInterface;
use App\Entity\SolanaContract;
use App\Infrastructure\Bus\SimpleQueryBus;
use App\Repository\SolanaContractRepository;
use Psr\Container\ContainerInterface;

class MockSolanaContractRepository extends SolanaContractRepository
{
    public function __construct() {}

    public function findWithRelations(int $id): ?SolanaContract
    {
        $contract = new SolanaContract();
        $contract->setTitle('Dummy Contract Response');
        return $contract;
    }
}

class SimpleContainer implements ContainerInterface
{
    private array $services = [];

    public function set(string $id, object $service): void
    {
        $this->services[$id] = $service;
    }

    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new \Exception("Service $id not found");
        }
        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}

echo "Starting Handler Unit Test with Dummy Response via Bus...\n";

// 1. Create Mock Repository
$mockRepo = new MockSolanaContractRepository();

// 2. Instantiate Handler
$handler = new GetSolanaContractByIdHandler($mockRepo);

// 3. Setup Bus & Container
$container = new SimpleContainer();
// The bus resolves handler class name from query class name.
// App\Application\Query\GetSolanaContractByIdQuery
// -> App\Application\QueryHandler\GetSolanaContractByIdHandler
$container->set(GetSolanaContractByIdHandler::class, $handler);

$bus = new SimpleQueryBus($container);

// 4. Create Query
$query = new GetSolanaContractByIdQuery(123);

// 5. Execute via Bus
try {
    $result = $bus->ask($query);

    // 6. Assert Response
    if ($result instanceof SolanaContract) {
        if ($result->getTitle() === 'Dummy Contract Response') {
            echo "SUCCESS: Dummy Response verified via Bus.\n";
        } else {
            echo "FAILURE: Title mismatch. Got: " . $result->getTitle() . "\n";
            exit(1);
        }
    } else {
        echo "FAILURE: Result is not a SolanaContract.\n";
        var_dump($result);
        exit(1);
    }
} catch (\Throwable $e) {
    echo "FAILURE: Bus execution error: " . $e->getMessage() . "\n";
    exit(1);
}
