<?php

declare(strict_types=1);

namespace Kaiseki\Config\Exception;

use RuntimeException;

final class UnknownKeyException extends RuntimeException
{
    public static function fromKey(string $key): self
    {
        return new self(\Safe\sprintf('Unknown config key "%s".', $key));
    }
}
