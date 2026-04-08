<?php

namespace App\Application\SolanaContract\Command;

use App\Contract\Cqrs\InvalidatesCacheInterface;
use App\Entity\SolanaContract;
use App\Entity\User;

class CreateSolanaContractCommand implements InvalidatesCacheInterface
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

    public function getCacheKeysToInvalidate(): array
    {
        // At the moment of command creation, contract may not have ID yet.
        // As an academic example, we could clear 'user_contracts_' pages or just return an empty array if we don't know keys.
        // We return an empty array or basic known keys
        return [];
    }
}
