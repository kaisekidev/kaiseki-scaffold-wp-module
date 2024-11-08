<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\ScaffoldModule;

use Kaiseki\Config\Config;
use Psr\Container\ContainerInterface;

final class FeatureNameFactory
{
    public function __invoke(ContainerInterface $container): FeatureName
    {
        $config = Config::fromContainer($container);

        return new FeatureName(
            $config->string('scaffold_module.feature_notice')
        );
    }
}
