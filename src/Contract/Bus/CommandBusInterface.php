<?php

namespace App\Contract\Bus;

use App\Contract\Cqrs\CommandInterface;

interface CommandBusInterface
{
    /**
     * Dispatches a command to its handler.
     */
    public function dispatch(CommandInterface $command): void;
}
