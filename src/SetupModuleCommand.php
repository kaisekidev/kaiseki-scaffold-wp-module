<?php

declare(strict_types=1);

namespace Kaiseki\ScaffoldModule;

use Laminas\Filter\Word\DashToCamelCase;
use Laminas\Filter\Word\DashToUnderscore;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'setup-module',
)]
class SetupModuleCommand extends Command
{
    private TypeEnum $type;
    private string $moduleName;
    private string $configBaseKey;
    private string $namespace;
    private string $repoUrl;
    private string $copyrightHolder;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $question = $this->getHelper('question');
        $this
            ->askForType($input, $output, $question)
            ->askForModuleName($input, $output, $question)
            ->askForConfigBase($input, $output, $question)
            ->askForNamespace($input, $output, $question)
            ->askForRepoUrl($input, $output, $question)
            ->askForCopyrightHolder($input, $output, $question);

        $sharedFiles = $this->getAllFilesInDirectory(__DIR__ . '/../templates/shared');
        $typeFiles = $this->getAllFilesInDirectory(__DIR__ . '/../templates/' . $this->getTypeFolder());

        $this->copyFiles(array_merge($sharedFiles, $typeFiles));

        return Command::SUCCESS;
    }

    private function copyFiles(array $paths): void
    {
        foreach ($paths as $path) {
            $fileContent = new FileContent($path);
            $fileContent
                ->searchReplaceString(
                    $this->getArgTemplate('package_name_dash'),
                    $this->getModulePrefix() . $this->moduleName
                )
                ->searchReplaceString($this->getArgTemplate('config_base_key'), $this->configBaseKey)
                ->searchReplaceString($this->getArgTemplate('namespace'), $this->namespace)
                ->searchReplaceString($this->getArgTemplate('namespace_escaped'), str_replace('\\', '\\\\', $this->namespace))
                ->searchReplaceString($this->getArgTemplate('repo_url'), $this->repoUrl)
                ->searchReplaceString($this->getArgTemplate('copyright_holder'), $this->copyrightHolder)
                ->writeToFile($this->getOutputPath($path), basename($path));
        }
    }

    private function getOutputPath(string $path): string
    {
        $path = pathinfo($path, PATHINFO_DIRNAME);
        $dir = realpath(__DIR__ . '/../templates/');
        $escapedDir = preg_quote($dir, '/');
        return preg_replace(
            '/'. $escapedDir . '\/(core|wordpress|shared)/',
            realpath(__DIR__ . '/..') . '/output',
            $path
        );
    }

    private function askForType(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): self
    {
        $question = new ChoiceQuestion(
            'Which type of module do you want to create (defaults to "wordpress")',
            ['wordpress', 'core'],
            0
        );

        $type = $questionHelper->ask($input, $output, $question);

        $this->type = $type === 'wordpress' ? TypeEnum::WORDPRESS : TypeEnum::CORE;

        return $this;
    }

    private function askForModuleName(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): self
    {
        $moduleName = $this->guessModuleName();

        $question = new Question(
            sprintf(
                'Module name (kaiseki/%s*) [default: %s]: ',
                $this->getModulePrefix(),
                $moduleName
            ),
            $moduleName
        );
        $question->setValidator(function (string $answer): string {
            if (preg_match('/^[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*$/', $answer) !== 1) {
                throw new \RuntimeException(sprintf('%s is not a valid package name.', $answer));
            }

            return $answer;
        });
        $question->setMaxAttempts(3);

        $this->moduleName = $questionHelper->ask($input, $output, $question);

        $output->writeln('Module name: ' . $this->moduleName);

        return $this;
    }

    private function askForConfigBase(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): self
    {
        $default = (new DashToUnderscore())->filter($this->moduleName);

        $question = new Question(
            sprintf(
                'Config base key [default: %s]: ',
                $default
            ),
            $default
        );

        $this->configBaseKey = $questionHelper->ask($input, $output, $question);

        $output->writeln('Config base key: ' . $this->configBaseKey);

        return $this;
    }

    private function askForNamespace(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): self
    {
        $namespace = (new DashToCamelCase())->filter($this->moduleName);
        $prefix = $this->type === TypeEnum::WORDPRESS ? 'WordPress\\' : '';

        $question = new Question(
            sprintf(
                'Module namespace (Kaiseki\\%s*) [default: %s]: ',
                $prefix,
                $namespace
            ),
            $namespace
        );
        $question->setValidator(function (string $answer): string {
            if (preg_match('/^[A-Z][A-Za-z0-9]*$/', $answer) !== 1) {
                throw new \RuntimeException(sprintf('%s is not a valid namespace.', $answer));
            }

            return $answer;
        });
        $question->setMaxAttempts(3);

        $this->namespace = $questionHelper->ask($input, $output, $question);

        $output->writeln('Namespace: ' . $this->namespace);

        return $this;
    }

    private function askForRepoUrl(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): self
    {
        $url = 'https://github.com/kaisekidev/' . $this->getModulePrefix() . $this->moduleName;

        $question = new Question(
            sprintf(
                'URL to repository [default: %s]: ',
                $url
            ),
            $url
        );
        $question->setValidator(function (string $answer): string {
            if (!filter_var($answer, FILTER_VALIDATE_URL) !== false) {
                throw new \RuntimeException(sprintf('%s is not a URL.', $answer));
            }

            return $answer;
        });
        $question->setMaxAttempts(3);

        $this->repoUrl = $questionHelper->ask($input, $output, $question);

        $output->writeln('Repository URL: ' . $this->repoUrl);

        return $this;
    }

    private function askForCopyrightHolder(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): self
    {
        $default = 'woda - Software Development GmbH';
        $question = new Question(
            sprintf(
                'Copyright holder [default: %s]: ',
                $default
            ),
            $default
        );

        $this->copyrightHolder = (string)$questionHelper->ask($input, $output, $question);

        $output->writeln('Copyright Holder: ' . $this->repoUrl);

        return $this;
    }

    private function guessModuleName(): string
    {
        return str_replace(['kaiseki-', 'wp-'], '', basename(dirname(__DIR__)));
    }

    private function getAllFilesInDirectory(string $directory): array
    {
        $result = [];

        if (!is_dir($directory)) {
            return $result;
        }

        // Use DirectoryIterator to iterate over directory contents
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) { // Check if it is a file
                $result[] = realpath($fileInfo->getPathname());
            }
        }

        return $result;
    }

    private function getArgTemplate(string $key): string
    {
        return "%{$key}%";
    }

    private function getTypeFolder(): string
    {
        return $this->type === TypeEnum::WORDPRESS ? 'wordpress' : 'core';
    }

    private function getModulePrefix(): string
    {
        return $this->type === TypeEnum::WORDPRESS ? 'wp-' : '';
    }
}