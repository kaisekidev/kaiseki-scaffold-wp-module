<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\ScaffoldModule;

use Kaiseki\WordPress\Hook\HookProviderInterface;

use function add_action;
use function trim;

final class FeatureName implements HookProviderInterface
{
    public function __construct(private readonly string $notice)
    {
    }

    public function addHooks(): void
    {
        add_action('admin_notices', [$this, 'displayNotice']);
    }

    public function displayNotice(): void
    {
        if (trim($this->notice) === '') {
            return;
        }

        $notice = \Safe\sprintf(
            __('Kaiseki module %s is active', 'kaiseki'),
            $this->notice
        );

        ?>
        <div class="notice notice-success is-dismissible">
            <p><?= $notice ?></p>
        </div>
        <?php
    }
}
