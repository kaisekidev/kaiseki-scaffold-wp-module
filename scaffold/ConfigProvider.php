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
            'package-name' => [
                'feature_notice' => 'Hello World',
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
