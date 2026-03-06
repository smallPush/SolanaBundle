<?php

namespace App\Infrastructure\Cache;

use Psr\Cache\InvalidArgumentException as PsrInvalidArgumentException;

class InvalidArgumentException extends \InvalidArgumentException implements PsrInvalidArgumentException
{
}
