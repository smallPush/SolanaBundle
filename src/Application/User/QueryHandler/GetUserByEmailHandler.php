<?php

namespace App\Application\User\QueryHandler;

use App\Application\User\Query\GetUserByEmailQuery;
use App\Contract\Cqrs\QueryHandlerInterface;
use App\Contract\Cqrs\QueryInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class GetUserByEmailHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(QueryInterface $query): ?User
    {
        if (!$query instanceof GetUserByEmailQuery) {
            throw new \InvalidArgumentException('Expected GetUserByEmailQuery');
        }

        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $query->getEmail()]);
    }
}
