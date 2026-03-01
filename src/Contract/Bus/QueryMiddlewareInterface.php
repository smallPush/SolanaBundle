<?php

namespace App\Contract\Bus;

use App\Contract\Cqrs\QueryInterface;

interface QueryMiddlewareInterface
{
    /**
     * Handle the query and call the next middleware in the chain.
     *
     * @param QueryInterface $query
     * @param callable $next
     * @return mixed
     */
    public function handle(QueryInterface $query, callable $next): mixed;
}
