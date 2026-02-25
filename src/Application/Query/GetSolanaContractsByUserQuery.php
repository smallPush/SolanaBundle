<?php

namespace App\Application\Query;

use App\Contract\Cqrs\QueryInterface;
use App\Entity\User;

class GetSolanaContractsByUserQuery implements QueryInterface
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
