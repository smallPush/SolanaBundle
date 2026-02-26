<?php

namespace App\Contract\Cqrs;

interface CommandHandlerInterface
{
    /**
     * Handles the command.
     */
    public function __invoke(CommandInterface $command): void;
}
