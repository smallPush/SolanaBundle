<?php

namespace App\Application\SolanaContract\Command;

use App\Contract\Cqrs\InvalidatesCacheInterface;
use App\Entity\SolanaContract;
use App\Entity\User;

class ValidateSolanaContractCommand implements InvalidatesCacheInterface
{
    private SolanaContract $contract;
    private User $validator;

    public function __construct(SolanaContract $contract, User $validator)
    {
        $this->contract = $contract;
        $this->validator = $validator;
    }

    public function getContract(): SolanaContract
    {
        return $this->contract;
    }

    public function getValidator(): User
    {
        return $this->validator;
    }

    public function getCacheKeysToInvalidate(): array
    {
        return [
            'solana_contract_' . $this->contract->getId()
        ];
    }
}
