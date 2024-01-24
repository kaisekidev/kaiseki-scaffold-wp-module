<?php

declare(strict_types=1);

use Kaiseki\CodingStandard\PhpCsFixerConfig;
use PhpCsFixer\Finder;

$finder = new Finder();

$finder
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/scaffold')
    ->files()
    ->name('*.php');

return PhpCsFixerConfig::get($finder);

