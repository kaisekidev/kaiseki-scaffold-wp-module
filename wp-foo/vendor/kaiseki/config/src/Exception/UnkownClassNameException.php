<?php

declare(strict_types=1);

namespace Kaiseki\Config\Exception;

use RuntimeException;

final class UnkownClassNameException extends RuntimeException
{
    public static function fromName(string $name): self
    {
        return new self(\Safe\sprintf('Unknown class name "%s".', $name));
    }
}
