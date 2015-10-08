<?php
namespace Spanky\Versioner\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SetVersionCommand
 *
 * @package Spanky\Versioner
 */
class SetVersionCommand extends AbstractVersionerCommand
{
    /**
     * Configure the command for the console app.
     */
    protected function configure()
    {
        $this->setName('set');
        $this->setDescription('Set the current version of the project.');

        $this->addArgument(
            'version',
            InputArgument::REQUIRED,
            'The SemVer version to set.'
        );
    }

    /**
     * Set the version of the app.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            // Attempt to set the current version based on the version passed in
            $version = $this->versioner->setCurrentVersionFromString($input->getArgument('version'));

            // Output that the current version has been changed
            $output->writeln(sprintf(
                '<comment>Set the current version to</comment> <info>%s</info>',
                $version
            ));

        } catch (\InvalidArgumentException $e) {
            // Invalid version string was supplied
            $output->writeln('<error>Version does not appear to be a valid SemVer version.</error>');
        } catch (Exception $e) {
            // Any other exception, like failing to write to the path
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
