<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\ScaffoldModule;

final class ConfigProvider
{
    /**
     * @return array<mixed>
     */
    public function __invoke(): array
    {
        return [
            'package_name' => [
                'feature_notice' => 'wp-scaffold-module',
            ],
            'hook' => [
                'provider' => [
                    FeatureName::class,
                ],
            ],
            'dependencies' => [
                'aliases' => [],
                'factories' => [
                    FeatureName::class => FeatureNameFactory::class,
                ],
            ],
        ];
    }
}
