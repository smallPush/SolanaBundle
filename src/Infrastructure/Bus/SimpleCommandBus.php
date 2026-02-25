<?php

namespace App\Infrastructure\Bus;

use App\Contract\Bus\CommandBusInterface;
use App\Contract\Cqrs\CommandInterface;
use Psr\Container\ContainerInterface;

class SimpleCommandBus implements CommandBusInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function dispatch(CommandInterface $command): void
    {
        $handlerClass = $this->getHandlerClass($command);

        if (!$this->container->has($handlerClass)) {
            // Attempt to instantiate if not in container (fallback for simple cases without autowiring)
            // But handlers usually need dependencies (EntityManager, etc.)
            // So we really need them in the container.
            throw new \RuntimeException(sprintf('Handler "%s" for command "%s" not found in container.', $handlerClass, get_class($command)));
        }

        $handler = $this->container->get($handlerClass);
        $handler($command);
    }

    private function getHandlerClass(CommandInterface $command): string
    {
        $commandClass = get_class($command);
        // App\Application\Command\CreateSolanaContractCommand
        // -> App\Application\CommandHandler\CreateSolanaContractHandler

        // 1. Replace namespace segment
        $handlerClass = str_replace('\\Command\\', '\\CommandHandler\\', $commandClass);

        // 2. Replace class name suffix
        if (substr($handlerClass, -7) === 'Command') {
            $handlerClass = substr($handlerClass, 0, -7) . 'Handler';
        }

        return $handlerClass;
    }
}
