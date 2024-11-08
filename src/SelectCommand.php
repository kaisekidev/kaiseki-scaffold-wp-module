<?php


namespace Kaiseki\ScaffoldModule;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class SelectCommand extends Command
{
    // Command name and description
    protected static $defaultName = 'app:select';

    protected function configure()
    {
        $this
            ->setDescription('Select either "core" or "wp"')
            ->setHelp('This command allows you to select between a list of options: "core" or "wp"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create the question helper
        $helper = $this->getHelper('question');

        // Define the question
        $question = new ChoiceQuestion(
            'Please select your option (defaults to "core")',
            ['core', 'wp'],
            0 // Default to the first option (indexed at 0)
        );

        // Set the question to allow preview of the selected option
        $question->setErrorMessage('Option %s is invalid.');

        // Ask the question
        $selection = $helper->ask($input, $output, $question);

        // Display the selected option
        $output->writeln('You have selected: ' . $selection);

        return Command::SUCCESS;
    }
}
