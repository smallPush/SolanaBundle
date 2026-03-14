<?php

namespace App\Infrastructure\Cache;

use Psr\Cache\CacheException as PsrCacheException;

class CacheException extends \RuntimeException implements PsrCacheException
{
}
