<?php

namespace App\Infrastructure\Bus;

use App\Contract\Bus\CommandBusInterface;
use App\Contract\Bus\Locator\HandlerLocatorInterface;
use App\Contract\Cqrs\CommandInterface;

class SimpleCommandBus implements CommandBusInterface
{
    private HandlerLocatorInterface $locator;
    private iterable $middlewares;

    public function __construct(HandlerLocatorInterface $locator, iterable $middlewares = [])
    {
        $this->locator = $locator;
        $this->middlewares = $middlewares;
    }

    public function dispatch(CommandInterface $command): void
    {
        $handler = $this->locator->getHandler($command);

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
}
