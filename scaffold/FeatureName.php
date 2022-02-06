<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\ModuleName;

use Kaiseki\WordPress\Hook\HookCallbackProviderInterface;

use function add_action;
use function trim;

final class FeatureName implements HookCallbackProviderInterface
{
    private string $notice;

    public function __construct(string $notice)
    {
        $this->notice = $notice;
    }

    public function registerCallbacks(): void
    {
        add_action('admin_notices', [$this, 'displayNotice']);
    }

    public function init(): void
    {
        if (trim($this->notice) === '') {
            return;
        }
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?= $this->notice; ?></p>
        </div>
        <?php
    }
}
