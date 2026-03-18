<?php

namespace App\Application\SolanaContract\QueryHandler;

use App\Application\SolanaContract\Query\GetSolanaContractByIdQuery;
use App\Contract\Cqrs\QueryHandlerInterface;
use App\Contract\Cqrs\QueryInterface;
use App\Repository\SolanaContractRepository;

class GetSolanaContractByIdHandler implements QueryHandlerInterface
{
    private SolanaContractRepository $repository;

    public function __construct(SolanaContractRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(QueryInterface $query): mixed
    {
        if (!$query instanceof GetSolanaContractByIdQuery) {
            throw new \InvalidArgumentException('Expected GetSolanaContractByIdQuery');
        }

        return $this->repository->findWithRelations($query->getId());
    }
}
