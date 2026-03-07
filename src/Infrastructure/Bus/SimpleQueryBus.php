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
        $middlewareArray = is_array($middlewares) ? $middlewares : iterator_to_array($middlewares);

        $pipeline = array_reduce(
            array_reverse($middlewareArray),
            function (callable $next, $middleware) {
                return function (QueryInterface $query) use ($middleware, $next) {
                    return $middleware->handle($query, $next);
                };
            },
            $coreHandler
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
