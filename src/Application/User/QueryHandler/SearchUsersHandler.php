<?php

namespace App\Application\User\QueryHandler;

use App\Application\User\Query\SearchUsersQuery;
use App\Contract\Cqrs\QueryHandlerInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SearchUsersHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(SearchUsersQuery $query): array
    {
        $q = $query->getQ();

        return $this->entityManager->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.email LIKE :q')
            ->setParameter('q', '%' . $q . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
