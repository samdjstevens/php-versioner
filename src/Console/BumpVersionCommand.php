<?php
namespace Spanky\Versioner\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BumpVersionCommand
 *
 * @package Spanky\Versioner
 */
class BumpVersionCommand extends AbstractVersionerCommand
{
    /**
     * Configure the command for the console app.
     */
    protected function configure()
    {
        $this->setName('bump');
        $this->setDescription('Bump the current version of the project.');

        $this->addOption(
            'patch',
            null,
            InputOption::VALUE_NONE,
            'Specify this option to bump the patch number.'
        );

        $this->addOption(
            'minor',
            null,
            InputOption::VALUE_NONE,
            'Specify this option to bump the minor number.'
        );

        $this->addOption(
            'major',
            null,
            InputOption::VALUE_NONE,
            'Specify this option to bump the major number.'
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
            // Get the current SemVerVersion object for the project
            $version = $this->versioner->getCurrentVersion();

            // Get the next version based on what we are bumping
            switch ($this->getVersionToBump($input)) {
                case 'major':
                    $newVersion = $version->getNextMajorVersion();
                    break;
                case 'minor':
                    $newVersion = $version->getNextMinorVersion();
                    break;
                case 'patch':
                    $newVersion = $version->getNextPatchVersion();
                    break;
            }

            // Set the new bumped version
            $this->versioner->setCurrentVersion($newVersion);

            // Output that the current version has been changed
            $output->writeln(sprintf(
                '<comment>Bumped the version from</comment> <info>%s</info> <comment>to</comment> <info>%s</info>',
                $version,
                $newVersion
            ));

        } catch (\InvalidArgumentException $e) {
            // Invalid version string was supplied
            $output->writeln('<error>Version does not appear to be a valid SemVer version.</error>');
        } catch (Exception $e) {
            // Any other exception, like failing to write to the path
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     * Determine what part of the version is to be bumped up.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return string
     */
    protected function getVersionToBump(InputInterface $input)
    {
        // If major was specified, then bump that
        if ($input->getOption('major')) {
            return 'major';
        }

        // Otherwise, if minor was specified, then go with that
        if ($input->getOption('minor')) {
            return 'minor';
        }

        // If major/minor not specified, them bump patch
        return 'patch';
    }
}
