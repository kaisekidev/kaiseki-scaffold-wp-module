<?php

declare(strict_types=1);

namespace Kaiseki\Config;

use Kaiseki\Config\Exception\UnkownClassNameException;
use Psr\Container\ContainerInterface;

use function is_object;

final class Config
{
    /**
     * @param ContainerInterface $container
     */
    public static function fromContainer(ContainerInterface $container): NestedArrayConfig
    {
        return new NestedArrayConfig((array)$container->get('config'));
    }

    /**
     * @template T of object
     *
     * @phpstan-param array<array-key, class-string<T>|T> $map
     *
     * @phpstan-return array<array-key, T>
     *
     * @param ContainerInterface $container
     * @param array              $map
     */
    public static function initClassMap(ContainerInterface $container, array $map): array
    {
        $init = [];
        foreach ($map as $key => $value) {
            /** @phpstan-var T $object */
            $object = self::initClass($container, $value);
            $init[$key] = $object;
        }

        return $init;
    }

    /**
     * @template T of object
     *
     * @phpstan-param class-string<T>|T $value
     *
     * @phpstan-return T
     *
     * @param ContainerInterface $container
     * @param object|string      $value
     */
    public static function initClass(ContainerInterface $container, string|object $value): object
    {
        if (is_object($value)) {
            return $value;
        }
        if (!$container->has($value)) {
            throw UnkownClassNameException::fromName($value);
        }
        /** @phpstan-var T $object */
        $object = $container->get($value);

        return $object;
    }
}
