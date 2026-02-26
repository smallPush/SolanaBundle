<?php

namespace App\Application\Query;

use App\Contract\Cqrs\QueryInterface;
use App\Entity\User;

class GetSolanaContractsByUserQuery implements QueryInterface
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
}
