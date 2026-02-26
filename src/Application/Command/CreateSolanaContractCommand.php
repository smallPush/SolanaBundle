<?php

namespace App\Application\Command;

use App\Contract\Cqrs\CommandInterface;
use App\Entity\SolanaContract;
use App\Entity\User;

class CreateSolanaContractCommand implements CommandInterface
{
    private SolanaContract $contract;
    private User $author;

    public function __construct(SolanaContract $contract, User $author)
    {
        $this->contract = $contract;
        $this->author = $author;
    }

    public function getContract(): SolanaContract
    {
        return $this->contract;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }
}
