<?php

namespace App\Tests;

// 1. Define Missing Interfaces/Classes for Doctrine if they don't exist
// This mimics the 'stubs' but ensures they are available for this specific test run.

if (!interface_exists('Doctrine\ORM\EntityManagerInterface')) {
    eval('namespace Doctrine\ORM; interface EntityManagerInterface { public function getRepository($className); }');
}

if (!class_exists('Doctrine\ORM\EntityRepository')) {
    eval('namespace Doctrine\ORM; class EntityRepository { public function createQueryBuilder($alias) {} }');
}

if (!class_exists('Doctrine\ORM\QueryBuilder')) {
    eval('namespace Doctrine\ORM; class QueryBuilder {
        public function select($select = null) { return $this; }
        public function where($where) { return $this; }
        public function setParameter($key, $value) { return $this; }
        public function getQuery() { return new Query(); }
        public function setFirstResult($firstResult) { return $this; }
        public function setMaxResults($maxResults) { return $this; }
    }');
}

if (!class_exists('Doctrine\ORM\Query')) {
    eval('namespace Doctrine\ORM; class Query {
        public function getResult() { return []; }
        public function getSingleScalarResult() { return 0; }
    }');
}

namespace App\Tests;

require_once __DIR__ . '/../vendor/autoload.php';
// Include stubs for Collections
require_once __DIR__ . '/stubs/Doctrine/Common/Collections/Collection.php';
require_once __DIR__ . '/stubs/Doctrine/Common/Collections/ArrayCollection.php';


use App\Application\Query\GetSolanaContractsByUserQuery;
use App\Application\QueryHandler\GetSolanaContractsByUserHandler;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use App\Contract\Psr\Cache\CacheItemPoolInterface;
use App\Contract\Psr\Cache\CacheItemInterface;

class PaginationOptimizationTest
{
    private $entityManager;
    private $cache;
    private $queryBuilder;
    private $query;

    public function run()
    {
        echo "Running PaginationOptimizationTest...\n";

        $this->setupMocks();

        try {
            $this->testPaginationLogic();
            echo "PASS: Pagination logic verified.\n";
        } catch (\Throwable $e) {
            echo "FAIL: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
            exit(1);
        }
    }

    private function setupMocks()
    {
        // Mock Query
        $this->query = new class extends Query {
            public function getResult() { return []; }
            public function getSingleScalarResult() { return 100; } // Mock total count
        };

        // Mock QueryBuilder
        $this->queryBuilder = new class($this->query) extends QueryBuilder {
            private $query;
            public $firstResult;
            public $maxResults;

            public function __construct($query) { $this->query = $query; }

            public function select($select = null) { return $this; }
            public function where($where) { return $this; }
            public function setParameter($key, $value) { return $this; }
            public function getQuery() { return $this->query; }

            public function setFirstResult($firstResult) {
                $this->firstResult = $firstResult;
                return $this;
            }

            public function setMaxResults($maxResults) {
                $this->maxResults = $maxResults;
                return $this;
            }
        };

        // Mock Repository
        $repository = new class($this->queryBuilder) extends EntityRepository {
            private $qb;
            public function __construct($qb) { $this->qb = $qb; }
            public function createQueryBuilder($alias) { return $this->qb; }
        };

        // Mock EntityManager
        $this->entityManager = new class($repository) implements EntityManagerInterface {
            private $repo;
            public function __construct($repo) { $this->repo = $repo; }
            public function getRepository($className) { return $this->repo; }
        };

        // Mock Cache Item
        $cacheItem = new class implements CacheItemInterface {
            public function getKey(): string { return 'key'; }
            public function get(): mixed { return null; }
            public function isHit(): bool { return false; }
            public function set(mixed $value): static { return $this; }
            public function expiresAt(?\DateTimeInterface $expiration): static { return $this; }
            public function expiresAfter(int|\DateInterval|null $time): static { return $this; }
        };

        // Mock Cache
        $this->cache = new class($cacheItem) implements CacheItemPoolInterface {
            private $item;
            public function __construct($item) { $this->item = $item; }
            public function getItem(string $key): CacheItemInterface { return $this->item; }
            public function getItems(array $keys = []): iterable { return []; }
            public function hasItem(string $key): bool { return false; }
            public function clear(): bool { return true; }
            public function deleteItem(string $key): bool { return true; }
            public function deleteItems(array $keys): bool { return true; }
            public function save(CacheItemInterface $item): bool { return true; }
            public function saveDeferred(CacheItemInterface $item): bool { return true; }
            public function commit(): bool { return true; }
        };
    }

    private function testPaginationLogic()
    {
        $handler = new GetSolanaContractsByUserHandler($this->entityManager, $this->cache);
        $user = new User();

        // This will verify that the Query class accepts page/limit args
        // If the code isn't updated, this will likely throw or be ignored.
        // We expect page 2, limit 10. Offset should be (2-1)*10 = 10.
        $page = 2;
        $limit = 10;

        // Check if Query class supports extra args
        if (method_exists(GetSolanaContractsByUserQuery::class, 'getPage')) {
             $query = new GetSolanaContractsByUserQuery($user, $page, $limit);
        } else {
             throw new \Exception("GetSolanaContractsByUserQuery does not support pagination arguments yet.");
        }

        $result = $handler($query);

        // Verify QueryBuilder calls
        if ($this->queryBuilder->firstResult !== 10) {
            throw new \Exception("Expected firstResult to be 10, got " . var_export($this->queryBuilder->firstResult, true));
        }

        if ($this->queryBuilder->maxResults !== 10) {
            throw new \Exception("Expected maxResults to be 10, got " . var_export($this->queryBuilder->maxResults, true));
        }

        // Verify Return Type
        if (!class_exists('App\DTO\PaginatedResult')) {
             throw new \Exception("App\DTO\PaginatedResult class does not exist.");
        }

        if (!$result instanceof \App\DTO\PaginatedResult) {
            throw new \Exception("Handler did not return PaginatedResult instance.");
        }

        if ($result->page !== 2) {
             throw new \Exception("Result page incorrect.");
        }

        if ($result->total !== 100) {
             throw new \Exception("Result total incorrect.");
        }
    }
}

(new PaginationOptimizationTest())->run();
