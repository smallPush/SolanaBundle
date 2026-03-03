<?php

namespace App\Infrastructure\Bus;

use App\Contract\Bus\CommandBusInterface;
use App\Contract\Cqrs\CommandInterface;
use Psr\Container\ContainerInterface;

class SimpleCommandBus implements CommandBusInterface
{
    private ContainerInterface $container;
    private iterable $middlewares;

    public function __construct(ContainerInterface $container, iterable $middlewares = [])
    {
        $this->container = $container;
        $this->middlewares = $middlewares;
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

        $core = function (CommandInterface $command) use ($handler): void {
            $handler($command);
        };

        $middlewares = is_array($this->middlewares) ? $this->middlewares : iterator_to_array($this->middlewares);
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function (callable $next, $middleware) {
                return function (CommandInterface $command) use ($middleware, $next): void {
                    $middleware->handle($command, $next);
                };
            },
            $core
        );

        $pipeline($command);
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
