<?php

declare(strict_types=1);

namespace Kaiseki\CodingStandard\PhpCsFixer;

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Finder;
use RuntimeException;

use function getcwd;

final class Config
{
    public static function get(Finder|null $finder = null, array $rules = []): ConfigInterface
    {
        $finder ??= self::createFinder();
        return (new \PhpCsFixer\Config())
            ->setRules(array_merge(self::rules(), $rules))
            ->setFinder($finder)
            ->setRiskyAllowed(true);
    }

    /**
     * @return array<string, mixed>
     */
    private static function rules(): array
    {
        return require(__DIR__ . '/../../php-cs-fixer-rules.php');
    }

    private static function createFinder(): Finder
    {
        return Finder::create()->in(self::callingDir());
    }

    private static function callingDir(): string
    {
        $cwd = getcwd();
        if ($cwd === false || $cwd === '') {
            throw new RuntimeException('Could not determine the current base directory');
        }
        return $cwd;
    }
}
