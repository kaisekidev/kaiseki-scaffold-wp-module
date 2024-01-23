<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\ModuleName;

final class ConfigProvider
{
    /**
     * @return array<mixed>
     */
    public function __invoke(): array
    {
        return [
            'package_name' => [
                'feature_notice' => 'foo',
            ],
            'hook' => [
                'provider' => [
                    FeatureName::class,
                ]
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
