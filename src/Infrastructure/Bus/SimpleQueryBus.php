<?php

namespace App\Infrastructure\Bus;

use App\Contract\Bus\Locator\HandlerLocatorInterface;
use App\Contract\Bus\QueryBusInterface;
use App\Contract\Bus\QueryMiddlewareInterface;
use App\Contract\Cqrs\QueryInterface;

class SimpleQueryBus implements QueryBusInterface
{
    private HandlerLocatorInterface $locator;
    private iterable $middlewares;

    public function __construct(HandlerLocatorInterface $locator, iterable $middlewares = [])
    {
        $this->locator = $locator;
        $this->middlewares = $middlewares;
    }

    public function ask(QueryInterface $query): mixed
    {
        // The core handler execution is the last step in the pipeline.
        $coreHandler = function (QueryInterface $q) {
            $handler = $this->locator->getHandler($q);
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

        // Build the pipeline backwards
        $pipeline = $coreHandler;

        // Ensure middlewares are processed in order by wrapping them correctly
        foreach (array_reverse($middlewareArray) as $middleware) {
            $next = $pipeline;
            $pipeline = function (QueryInterface $q) use ($middleware, $next) {
                return $middleware->handle($q, $next);
            };
        }

        return $pipeline($query);
    }
}
