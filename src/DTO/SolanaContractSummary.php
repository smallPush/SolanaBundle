<?php

namespace App\DTO;

class SolanaContractSummary
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?string $status,
    ) {
    }
}
