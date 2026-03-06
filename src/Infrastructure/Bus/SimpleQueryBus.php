<?php

namespace App\Infrastructure\Bus;

use App\Contract\Bus\QueryBusInterface;
use App\Contract\Bus\QueryMiddlewareInterface;
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
        // The core handler execution is the last step in the pipeline.
        $coreHandler = function (QueryInterface $q) {
            $handlerClass = $this->getHandlerClass($q);

            if (!$this->container->has($handlerClass)) {
                throw new \RuntimeException(sprintf('Handler "%s" for query "%s" not found in container.', $handlerClass, get_class($q)));
            }

            $handler = $this->container->get($handlerClass);
            return $handler($q);
        };

        return $this->processPipeline($query, $this->middlewares, $coreHandler);
    }

    /**
     * @param QueryInterface $query
     * @param iterable<QueryMiddlewareInterface> $middlewares
     * @param callable $coreHandler
     * @return mixed
     */
    private function processPipeline(QueryInterface $query, iterable $middlewares, callable $coreHandler): mixed
    {
        // Convert iterable to an array for easy pointer management,
        // or just use iterator to build the onion.
        $middlewareArray = is_array($middlewares) ? $middlewares : iterator_to_array($middlewares);

        // Build the pipeline backwards
        $pipeline = $coreHandler;

        // Ensure middlewares are processed in order by wrapping them correctly
        foreach (array_reverse($middlewareArray) as $middleware) {
            $next = $pipeline;
            $pipeline = function (QueryInterface $q) use ($middleware, $next) {
                return $middleware->handle($q, $next);
            };
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
