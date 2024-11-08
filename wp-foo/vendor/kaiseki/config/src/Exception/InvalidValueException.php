<?php

declare(strict_types=1);

namespace Kaiseki\Config\Exception;

use InvalidArgumentException;

use function gettype;

final class InvalidValueException extends InvalidArgumentException
{
    public static function expectedStringFromKey(string $key, mixed $value): self
    {
        return new self(\Safe\sprintf('Expected string value for "%s" but found "%s".', $key, gettype($value)));
    }

    public static function expectedIntegerFromKey(string $key, mixed $value): self
    {
        return new self(\Safe\sprintf('Expected integer value for "%s" but found "%s".', $key, gettype($value)));
    }

    public static function expectedFloatFromKey(string $key, mixed $value): self
    {
        return new self(\Safe\sprintf('Expected float value for "%s" but found "%s".', $key, gettype($value)));
    }

    public static function expectedBooleanFromKey(string $key, mixed $value): self
    {
        return new self(\Safe\sprintf('Expected boolean value for "%s" but found "%s".', $key, gettype($value)));
    }

    public static function expectedArrayFromKey(string $key, mixed $value): self
    {
        return new self(\Safe\sprintf('Expected array value for "%s" but found "%s".', $key, gettype($value)));
    }

    public static function expectedCallableFromKey(string $key, mixed $value): self
    {
        return new self(\Safe\sprintf('Expected callable value for "%s" but found "%s".', $key, gettype($value)));
    }
}
