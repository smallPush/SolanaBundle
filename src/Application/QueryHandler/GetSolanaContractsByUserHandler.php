<?php

namespace App\Application\QueryHandler;

use App\Application\Query\GetSolanaContractsByUserQuery;
use App\Contract\Cqrs\QueryHandlerInterface;
use App\Contract\Cqrs\QueryInterface;
use App\Contract\Psr\Cache\CacheItemPoolInterface;
use App\DTO\SolanaContractSummary;
use App\Entity\SolanaContract;
use Doctrine\ORM\EntityManagerInterface;

class GetSolanaContractsByUserHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private CacheItemPoolInterface $cache;

    public function __construct(EntityManagerInterface $entityManager, CacheItemPoolInterface $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    public function __invoke(QueryInterface $query): mixed
    {
        if (!$query instanceof GetSolanaContractsByUserQuery) {
            throw new \InvalidArgumentException('Expected GetSolanaContractsByUserQuery');
        }

        $user = $query->getUser();
        $key = 'user_contracts_' . $user->getId();
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $contracts = $this->entityManager
            ->getRepository(SolanaContract::class)
            ->createQueryBuilder('s')
            ->select(sprintf('NEW %s(s.id, s.title, s.status)', SolanaContractSummary::class))
            ->where('s.author = :user OR s.donor = :user OR s.volunteer = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $cacheItem->set($contracts);
        $cacheItem->expiresAfter(60);
        $this->cache->save($cacheItem);

        return $contracts;
    }
}
