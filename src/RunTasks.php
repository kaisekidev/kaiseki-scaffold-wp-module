<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\ScaffoldModule;

use Composer\Script\Event;
use Laminas\Filter\Word\DashToCamelCase;
use LogicException;

use function array_filter;
use function basename;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function in_array;
use function is_array;
use function json_decode;
use function json_encode;
use function preg_match;
use function preg_replace_callback;
use function rename;
use function rmdir;
use function sprintf;
use function str_repeat;
use function str_replace;
use function strlen;
use function substr;
use function unlink;

use const ARRAY_FILTER_USE_KEY;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;

final class RunTasks
{
    private const UNUSED_PACKAGES = [
        'ext-json',
        'composer/composer',
        'laminas/laminas-filter',
    ];
    private const UNUSED_FILES = [
        __DIR__ . '/../README.md',
        __DIR__ . '/../test-create-module.sh',
        __FILE__,
    ];
    private const DIST_FILES = [
        __DIR__ . '/../.github/workflows/checks.yaml.dist',
        __DIR__ . '/../.github/workflows/dispatch-docs-change.yaml.dist',
        __DIR__ . '/../.gitattributes.dist',
        __DIR__ . '/../README.md.dist',
    ];
    private const SEARCH_REPLACE_FILES = [
        __DIR__ . '/../.gitignore',
        __DIR__ . '/../README.md.dist',
        __DIR__ . '/../.php-cs-fixer.dist.php',
        __DIR__ . '/../phpstan.neon',
        __DIR__ . '/../phpunit.xml',
        __DIR__ . '/../scaffold/ConfigProvider.php',
        __DIR__ . '/../scaffold/FeatureName.php',
        __DIR__ . '/../scaffold/FeatureNameFactory.php',
        __DIR__ . '/../tests/unit/ConfigProviderTest.php',
    ];

    private string $moduleName;
    private string $moduleNamespace;
    private string $composerPackageName;

    public static function init(Event $event): void
    {
        $io = $event->getIO();
        $moduleName = self::guessModuleName();
        /** @var string $moduleName */
        $moduleName = $io->askAndValidate(
            sprintf('Module name (kaiseki/wp-*) [default: %s]: ', $moduleName),
            [self::class, 'validateModuleName'],
            null,
            $moduleName
        );
        $moduleNamespace = (new DashToCamelCase())->filter($moduleName);
        /** @var string $moduleNamespace */
        $moduleNamespace = $io->askAndValidate(
            sprintf('Module namespace (Kaiseki\\WordPress\\*) [default: %s]: ', $moduleNamespace),
            [self::class, 'validateModuleNamespace'],
            null,
            $moduleNamespace
        );
        (new self($moduleName, $moduleNamespace))->run();
    }

    public static function validateModuleName(string $moduleName): string
    {
        // @see https://getcomposer.org/doc/04-schema.md#name
        if (preg_match('/^[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*$/', $moduleName) !== 1) {
            throw new LogicException(sprintf('%s is not a valid package name.', $moduleName));
        }
        return $moduleName;
    }

    public static function validateModuleNamespace(string $namespace): string
    {
        // Starts with upper case, then lower case, then backslash, then upper case, then lower case, repeatable
        if (preg_match('/^(?:[A-Z]{1}[a-zA-Z]*(?:\\\\(?![a-z]))?)+[a-zA-Z]$/', $namespace) !== 1) {
            throw new LogicException(sprintf('%s is not a valid package name.', $namespace));
        }
        return $namespace;
    }

    private static function guessModuleName(): string
    {
        return str_replace(['kaiseki-', 'wp-'], '', basename(dirname(__DIR__)));
    }

    public function __construct(string $moduleName, string $moduleNamespace)
    {
        $this->moduleName = $moduleName;
        $this->moduleNamespace = $moduleNamespace;
        $this->composerPackageName = 'kaiseki/wp-' . $this->moduleName;
    }

    public function run(): void
    {
        $this->updateComposerInfos();
        $this->removeUnusedDependencies();
        $this->removeUnusedComposerCommands();
        $this->fixAutoload();
        $this->searchAndReplaceFiles();
        $this->deleteUnusedFiles();
        $this->activateDistFiles();
        $this->moveScaffoldFiles();
    }

    private function updateComposerInfos(): void
    {
        $gitHubUrl = sprintf('https://github.com/kaisekidev/kaiseki-wp-%s', $this->moduleName);
        $this->modifyComposerJson(
            function (array $json) use ($gitHubUrl): array {
                $json['name'] = $this->composerPackageName;
                $json['type'] = 'library';
                $json['homepage'] = $gitHubUrl;
                $json['description'] = '';
                $json['support'] = [
                    'issues' => $gitHubUrl . '/issues',
                    'source' => $gitHubUrl,
                ];
                return $json;
            }
        );
    }

    private function removeUnusedDependencies(): void
    {
        $this->modifyComposerJson(
            function (array $json): array {
                foreach (['require', 'require-dev'] as $key) {
                    /** @var array<array-key, mixed>|null $data */
                    $data = $json[$key] ?? null;
                    if (!is_array($data)) {
                        continue;
                    }
                    $json[$key] = array_filter(
                        $data,
                        fn(string $packageName): bool => !in_array($packageName, self::UNUSED_PACKAGES, true),
                        ARRAY_FILTER_USE_KEY
                    );
                }
                return $json;
            }
        );
    }

    private function removeUnusedComposerCommands(): void
    {
        $this->modifyComposerJson(
            static function (array $json): array {
                // @phpstan-ignore-next-line
                unset($json['scripts']['post-create-project-cmd']);
                return $json;
            }
        );
    }

    private function fixAutoload(): void
    {
        $this->modifyComposerJson(
            function (array $json): array {
                // @phpstan-ignore-next-line
                unset($json['autoload']['psr-4']['Kaiseki\\WordPress\\ScaffoldModule\\']);
                // @phpstan-ignore-next-line
                unset($json['autoload']['psr-4']['Kaiseki\\WordPress\\ModuleName\\']);
                unset($json['autoload-dev']['psr-4']['Kaiseki\\Test\\Functional\\WordPress\\ModuleName\\']);
                unset($json['autoload-dev']['psr-4']['Kaiseki\\Test\\Unit\\WordPress\\ModuleName\\']);
                // @phpstan-ignore-next-line
                $json['autoload']['psr-4']['Kaiseki\\WordPress\\' . $this->moduleNamespace . '\\'] = 'src';
                $devFunctional = sprintf('Kaiseki\\Test\\Functional\\WordPress\\%s\\', $this->moduleNamespace);
                $json['autoload-dev']['psr-4'][$devFunctional] = 'tests/functional';
                $devUnit = sprintf('Kaiseki\\Test\\Unit\\WordPress\\%s\\', $this->moduleNamespace);
                $json['autoload-dev']['psr-4'][$devUnit] = 'tests/unit';
                return $json;
            }
        );
    }

    private function deleteUnusedFiles(): void
    {
        foreach (self::UNUSED_FILES as $filename) {
            unlink($filename);
        }
    }

    private function activateDistFiles(): void
    {
        foreach (self::DIST_FILES as $filename) {
            $newName = substr($filename, 0, -5);
            rename($filename, $newName);
        }
    }

    private function moveScaffoldFiles(): void
    {
        rename(__DIR__ . '/../scaffold/ConfigProvider.php', __DIR__ . '/../src/ConfigProvider.php');
        rename(__DIR__ . '/../scaffold/FeatureName.php', __DIR__ . '/../src/FeatureName.php');
        rename(__DIR__ . '/../scaffold/FeatureNameFactory.php', __DIR__ . '/../src/FeatureNameFactory.php');
        rmdir(__DIR__ . '/../scaffold');
    }

    /**
     * @param callable(array<array-key, mixed>): array<array-key, mixed> $modify
     */
    private function modifyComposerJson(callable $modify): void
    {
        $this->modifyFile(
            __DIR__ . '/../composer.json',
            static function (string $contents) use ($modify): string {
                /** @var array<array-key, mixed> $composerArray */
                $composerArray = json_decode($contents, true);
                $composerArray = $modify($composerArray);
                $composerJson = json_encode($composerArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                return (string)preg_replace_callback(
                    '/^ +/m',
                    fn($m): string => str_repeat(' ', (int)(strlen($m[0]) / 2)),
                    $composerJson
                );
            }
        );
    }

    /**
     * @param string                   $filename
     * @param callable(string): string $modify
     */
    private function modifyFile(string $filename, callable $modify): void
    {
        $contents = file_get_contents($filename);
        $contents = $modify($contents);
        file_put_contents($filename, $contents);
    }

    private function searchAndReplaceFiles(): void
    {
        $search = [
            'ModuleName',
            'module-name',
            "/test-module/\n",
            "package_name",
            "kaiseki-scaffold-wp-module",
        ];

        $replace = [
            $this->moduleNamespace,
            $this->composerPackageName,
            '',
            str_replace('-', '_', $this->moduleName),
            'wp-' . $this->moduleName,
            '',
            '',
            '',
        ];

        foreach (self::SEARCH_REPLACE_FILES as $filename) {
            $this->modifyFile($filename, fn(string $contents): string  => str_replace($search, $replace, $contents));
        }
    }
}
