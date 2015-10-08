<?php
namespace Spanky\Versioner\Console;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GetVersionCommand
 *
 * @package Spanky\Versioner
 */
class GetVersionCommand extends AbstractVersionerCommand
{
    /**
     * Configure the command for the console app.
     */
    protected function configure()
    {
        $this->setName('current');
        $this->setDescription('Get the current version of the project.');
    }

    /**
     * Output the current version of the project.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            // Check to see if the version file is empty before trying to parse it
            if ($this->versioner->versionFileIsEmpty()) {
                $output->writeln(
                    '<comment>The version file is currently empty. Use the set command to set the version'
                    . ' on your project.</comment>'
                );

                return;
            }

            // Get the current version from the file
            $version = $this->versioner->getCurrentVersion();

            // Output the version number
            $output->writeln(sprintf(
                '<comment>The current version is</comment> <info>%s</info>',
                $version
            ));

        } catch (Exception $e) {
            // Something went wrong, output the error
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
