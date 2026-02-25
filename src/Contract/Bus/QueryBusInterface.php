<?php

namespace App\Contract\Bus;

use App\Contract\Cqrs\QueryInterface;

interface QueryBusInterface
{
    /**
     * Dispatches a query to its handler and returns the result.
     *
     * @return mixed
     */
    public function ask(QueryInterface $query): mixed;
}
