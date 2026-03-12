<?php

namespace App\Infrastructure\Bus\Locator;

use App\Contract\Bus\Locator\HandlerLocatorInterface;
use App\Contract\Cqrs\CommandInterface;
use App\Contract\Cqrs\QueryInterface;
use Psr\Container\ContainerInterface;

class ConventionBasedHandlerLocator implements HandlerLocatorInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getHandler(object $message): callable
    {
        $handlerClass = $this->getHandlerClass($message);

        if (!$this->container->has($handlerClass)) {
            throw new \RuntimeException(sprintf('Handler "%s" for message "%s" not found in container.', $handlerClass, get_class($message)));
        }

        return $this->container->get($handlerClass);
    }

    private function getHandlerClass(object $message): string
    {
        $messageClass = get_class($message);

        if ($message instanceof CommandInterface) {
            $handlerClass = str_replace('\\Command\\', '\\CommandHandler\\', $messageClass);
            if (substr($handlerClass, -7) === 'Command') {
                $handlerClass = substr($handlerClass, 0, -7) . 'Handler';
            }
            return $handlerClass;
        }

        if ($message instanceof QueryInterface) {
            $handlerClass = str_replace('\\Query\\', '\\QueryHandler\\', $messageClass);
            if (substr($handlerClass, -5) === 'Query') {
                $handlerClass = substr($handlerClass, 0, -5) . 'Handler';
            }
            return $handlerClass;
        }

        throw new \InvalidArgumentException(sprintf('Message of class "%s" is neither a Command nor a Query.', $messageClass));
    }
}
