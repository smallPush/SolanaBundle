<?php

namespace App\Application\User\Query;

use App\Contract\Cqrs\CacheableQueryInterface;

class SearchUsersQuery implements CacheableQueryInterface
{
    private string $q;

    public function __construct(string $q)
    {
        $this->q = $q;
    }

    public function getQ(): string
    {
        return $this->q;
    }

    public function getCacheKey(): string
    {
        return 'user_search_' . md5($this->q);
    }

    public function getCacheTtl(): int
    {
        return 60; // Cache for 60 seconds
    }
}
