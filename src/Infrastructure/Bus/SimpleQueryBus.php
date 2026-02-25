<?php

namespace App\Infrastructure\Bus;

use App\Contract\Bus\QueryBusInterface;
use App\Contract\Cqrs\QueryInterface;
use Psr\Container\ContainerInterface;

class SimpleQueryBus implements QueryBusInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function ask(QueryInterface $query): mixed
    {
        $handlerClass = $this->getHandlerClass($query);

        if (!$this->container->has($handlerClass)) {
            throw new \RuntimeException(sprintf('Handler "%s" for query "%s" not found in container.', $handlerClass, get_class($query)));
        }

        $handler = $this->container->get($handlerClass);
        return $handler($query);
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
