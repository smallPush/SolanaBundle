<?php

namespace App\Contract\Bus;

use App\Contract\Cqrs\QueryInterface;

interface QueryMiddlewareInterface
{
    /**
     * Handles the query and optionally calls the next middleware.
     *
     * @param QueryInterface $query The query to handle
     * @param callable(QueryInterface): mixed $next The next middleware in the chain
     *
     * @return mixed The result of the query
     */
    public function handle(QueryInterface $query, callable $next): mixed;
}
