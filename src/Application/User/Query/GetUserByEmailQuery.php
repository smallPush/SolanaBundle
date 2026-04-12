<?php

namespace App\Application\User\Query;

use App\Contract\Cqrs\CacheableQueryInterface;

class GetUserByEmailQuery implements CacheableQueryInterface
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCacheKey(): string
    {
        return 'user_email_' . md5($this->email);
    }

    public function getCacheTtl(): int
    {
        return 3600; // Cache for 1 hour
    }
}
