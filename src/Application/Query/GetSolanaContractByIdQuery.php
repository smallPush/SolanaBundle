<?php

namespace App\Application\Query;

use App\Contract\Cqrs\QueryInterface;

class GetSolanaContractByIdQuery implements QueryInterface
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
