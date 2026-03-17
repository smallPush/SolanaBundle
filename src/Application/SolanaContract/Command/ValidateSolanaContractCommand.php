<?php

namespace App\Application\SolanaContract\Command;

use App\Contract\Cqrs\CommandInterface;
use App\Entity\SolanaContract;
use App\Entity\User;

class ValidateSolanaContractCommand implements CommandInterface
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
}
