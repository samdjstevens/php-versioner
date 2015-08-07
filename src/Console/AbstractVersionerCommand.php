<?php
namespace Spanky\Versioner\Console;

use RuntimeException;
use Symfony\Component\Console\Command\Command;

/**
 * Class AbstractVersionerCommand
 *
 * @package Spanky\Versioner
 */
abstract class AbstractVersionerCommand extends Command
{
    /**
     * The path of the version.json file.
     *
     * @var string
     */
    protected $versionFilePath = './version.json';

    /**
     * Run a shell command on the local machine.
     *
     * @param  string $command
     * @return mixed
     * @throws \RuntimeException
     */
    protected function runShellCommand($command)
    {
        // Run the command
        $process = new Process($command);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
