<?php

namespace App\Application\SolanaContract\Query;

use App\Contract\Cqrs\CacheableQueryInterface;
use App\Entity\User;

class GetSolanaContractsByUserQuery implements CacheableQueryInterface
{
    private User $user;
    private int $page;
    private int $limit;

    public function __construct(User $user, int $page = 1, int $limit = 10)
    {
        $this->user = $user;
        $this->page = $page;
        $this->limit = $limit;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getCacheKey(): string
    {
        return 'user_contracts_' . $this->user->getId() . '_' . $this->page . '_' . $this->limit;
    }

    public function getCacheTtl(): int
    {
        return 60; // Cache for 60 seconds
    }
}
