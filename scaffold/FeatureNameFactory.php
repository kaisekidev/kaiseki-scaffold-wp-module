<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\ModuleName;

use Psr\Container\ContainerInterface;
use Kaiseki\WordPress\Config\Config;

final class FeatureNameFactory
{
    public function __invoke(ContainerInterface $container): FeatureName
    {
        $featureConfig = Config::get($container)->string('package-name/feature_notice');
        return new FeatureName($featureConfig);
    }
}
