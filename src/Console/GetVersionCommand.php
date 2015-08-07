<?php
namespace Spanky\Versioner\Console;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use Spanky\Versioner\SemVerVersion;
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
        $this->setName('versioner:current');
        $this->setDescription('Get the current version of the app.');
    }

    /**
     * Output the current version of the app.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            // Try to read the version from the version.json file
            $version = $this->readVersion();

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

    /**
     * Attempt to read the version from the version.js file, and return
     * a new SemVerVersion instance.
     *
     * @return \Spanky\Versioner\SemVerVersion
     * @throws \RuntimeException
     */
    protected function readVersion()
    {
        // If the version file does not exist, then create it for them
        if (! file_exists($this->versionFilePath)) {
            throw new RuntimeException(
                "The version.js files could not be found. Create one using the versioner:set [version] command."
            );
        }

        // Otherwise, we read it from the file..
        $versionJson = json_decode(file_get_contents($this->versionFilePath));

        // If the JSON decode failed, or the version property was not found,
        // then throw a RuntimeException
        if ($versionJson === false || ! isset($versionJson->version)) {
            throw new RuntimeException("The version.js files appears to corrupt.");
        }

        // Try to return a SemVerVersion from the version string found in the JSON.
        // If the version is an invalid, throw a RuntimeException.
        try {
            return SemVerVersion::fromString($versionJson->version);
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException("The version.js files appears to contain an invalid version number.");
        }
    }
}
