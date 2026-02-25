<?php

namespace App\Contract\Cqrs;

interface QueryHandlerInterface
{
    /**
     * Handles the query and returns the result.
     *
     * @return mixed
     */
    public function __invoke(QueryInterface $query): mixed;
}
