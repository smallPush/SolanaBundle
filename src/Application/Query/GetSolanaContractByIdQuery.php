<?php

namespace App\Application\Query;

use App\Contract\Cqrs\CacheableQueryInterface;

class GetSolanaContractByIdQuery implements CacheableQueryInterface
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

    public function getCacheKey(): string
    {
        return 'solana_contract_' . $this->id;
    }

    public function getCacheTtl(): int
    {
        return 3600; // Cache for 1 hour
    }
}
