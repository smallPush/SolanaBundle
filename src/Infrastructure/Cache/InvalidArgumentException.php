<?php

namespace App\Infrastructure\Cache;

use Psr\Cache\InvalidArgumentException as PsrInvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as PsrSimpleCacheInvalidArgumentException;

class InvalidArgumentException extends \InvalidArgumentException implements PsrInvalidArgumentException, PsrSimpleCacheInvalidArgumentException
{
}
