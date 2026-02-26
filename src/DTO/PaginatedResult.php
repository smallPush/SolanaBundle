<?php

namespace App\DTO;

class PaginatedResult
{
    /**
     * @param array<mixed> $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $limit,
        public int $pages,
    ) {
    }
}
