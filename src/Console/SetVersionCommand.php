<?php
namespace Spanky\Versioner\Console;

use RuntimeException;
use Spanky\Versioner\SemVerVersion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $this->setName('versioner:set');
        $this->setDescription('Set the current version of the app.');

        $this->addArgument(
            'version',
            InputArgument::REQUIRED,
            'The SemVer version to set.'
        );

        $this->addOption(
            'git',
            null,
            InputOption::VALUE_NONE,
            'If set, a Git commit + tag will be made.'
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

            // Try to create a new SemVerVersion object from the version passed in
            $version = SemVerVersion::fromString($input->getArgument('version'));

            // Try to update the version file with the new version
            $this->updateVersionFile($version);

            // Output that the current version has been changed
            $output->writeln(sprintf(
                '<comment>Set the current version to</comment> <info>%s</info>',
                $version
            ));

            // If the 'git' option was specified, then record the version
            // in git by making a commit with the new version file in it, and a tag
            if ($input->getOption('git')) {
                $this->recordVersionInGit($input, $output, $version);
            }

        } catch (\InvalidArgumentException $e) {

            // Invalid version string was supplied
            $output->writeln('<error>Version does not appear to be a valid SemVer version.</error>');

        } catch (Exception $e) {

            // Any other exception, like failing to write to the path
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     * Update the version file to contain the new version string.
     *
     * @param  \Spanky\Versioner\SemVerVersion $version
     * @throws Exception
     */
    protected function updateVersionFile(SemVerVersion $version)
    {
        if (! file_put_contents($this->versionFilePath, json_encode(['version' => (string) $version]))) {
            throw new RuntimeException("Failed to write to the version.json path.");
        }
    }

    /**
     * Record the version in git by making a commit containing the
     * version file and a tag of the version.
     *
     * @param \Spanky\Versioner\SemVerVersion $version
     */
    protected function recordVersionInGit(SemVerVersion $version)
    {
        // Create a git commit with the version file
        $this->runShellCommand(sprintf(
            'git add %s && git commit -m \'%s\'',
            $this->versionFilePath,
            'Set app version to ' . $version
        ));

        // Create a git tag of the version
        $this->runShellCommand(sprintf('git tag %s', $version));
    }
}
