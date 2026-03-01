<?php

namespace App\Application\QueryHandler;

use App\Application\Query\GetSolanaContractsByUserQuery;
use App\Contract\Cqrs\QueryHandlerInterface;
use App\Contract\Cqrs\QueryInterface;
use App\DTO\PaginatedResult;
use App\DTO\SolanaContractSummary;
use App\Entity\SolanaContract;
use Doctrine\ORM\EntityManagerInterface;

class GetSolanaContractsByUserHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(QueryInterface $query): mixed
    {
        if (!$query instanceof GetSolanaContractsByUserQuery) {
            throw new \InvalidArgumentException('Expected GetSolanaContractsByUserQuery');
        }

        $user = $query->getUser();
        $page = $query->getPage();
        $limit = $query->getLimit();

        $qb = $this->entityManager
            ->getRepository(SolanaContract::class)
            ->createQueryBuilder('s')
            ->where('s.author = :user OR s.donor = :user OR s.volunteer = :user')
            ->setParameter('user', $user);

        // Count query
        $countQb = clone $qb;
        $total = (int) $countQb->select('count(s.id)')->getQuery()->getSingleScalarResult();

        // Data query
        $contracts = $qb
            ->select(sprintf('NEW %s(s.id, s.title, s.status)', SolanaContractSummary::class))
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $result = new PaginatedResult(
            $contracts,
            $total,
            $page,
            $limit,
            (int) ceil($total / $limit)
        );

        return $result;
    }
}
