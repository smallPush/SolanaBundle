<?php

namespace App\Infrastructure\Bus;

use App\Contract\Bus\QueryBusInterface;
use App\Contract\Cqrs\QueryInterface;
use Psr\Container\ContainerInterface;

class SimpleQueryBus implements QueryBusInterface
{
    private ContainerInterface $container;
    private iterable $middlewares;

    public function __construct(ContainerInterface $container, iterable $middlewares = [])
    {
        $this->container = $container;
        $this->middlewares = $middlewares;
    }

    public function ask(QueryInterface $query): mixed
    {
        $handlerClass = $this->getHandlerClass($query);

        if (!$this->container->has($handlerClass)) {
            throw new \RuntimeException(sprintf('Handler "%s" for query "%s" not found in container.', $handlerClass, get_class($query)));
        }

        $handler = $this->container->get($handlerClass);

        $core = function (QueryInterface $query) use ($handler) {
            return $handler($query);
        };

        $middlewares = is_array($this->middlewares) ? $this->middlewares : iterator_to_array($this->middlewares);
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function (callable $next, $middleware) {
                return function (QueryInterface $query) use ($middleware, $next) {
                    return $middleware->handle($query, $next);
                };
            },
            $core
        );

        return $pipeline($query);
    }

    private function getHandlerClass(QueryInterface $query): string
    {
        $queryClass = get_class($query);
        // App\Application\Query\GetSolanaContractsByUserQuery
        // -> App\Application\QueryHandler\GetSolanaContractsByUserHandler

        // 1. Replace namespace segment
        $handlerClass = str_replace('\\Query\\', '\\QueryHandler\\', $queryClass);

        // 2. Replace class name suffix
        if (substr($handlerClass, -5) === 'Query') {
            $handlerClass = substr($handlerClass, 0, -5) . 'Handler';
        }

        return $handlerClass;
    }
}
