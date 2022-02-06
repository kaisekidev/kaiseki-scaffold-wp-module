<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\ModuleName;

use Kaiseki\WordPress\ModuleName\ConfigProvider;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testConfig(): void
    {
        $config = (new ConfigProvider())();

        self::assertArrayHasKey('dependencies', $config);
        self::assertArrayHasKey('hook', $config);
    }
}
