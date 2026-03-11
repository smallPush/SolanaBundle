<?php

namespace App\Contract\Bus\Locator;

interface HandlerLocatorInterface
{
    /**
     * Locate the handler for a given message (Command or Query).
     *
     * @param object $message The command or query object.
     * @return callable The handler instance capable of processing the message.
     * @throws \RuntimeException If the handler cannot be found.
     */
    public function getHandler(object $message): callable;
}
