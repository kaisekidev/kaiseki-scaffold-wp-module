<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\ModuleName;

use Kaiseki\Config\Config;
use Psr\Container\ContainerInterface;

final class FeatureNameFactory
{
    public function __invoke(ContainerInterface $container): FeatureName
    {
        $config = Config::fromContainer($container);
        return new FeatureName(
            $config->string('package_name.feature_notice')
        );
    }
}
