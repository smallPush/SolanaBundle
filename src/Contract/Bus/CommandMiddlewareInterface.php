<?php

namespace App\Contract\Bus;

use App\Contract\Cqrs\CommandInterface;

interface CommandMiddlewareInterface
{
    /**
     * Handle a command middleware.
     *
     * @param CommandInterface $command The command to handle.
     * @param callable $next The next middleware in the pipeline.
     */
    public function handle(CommandInterface $command, callable $next): void;
}
