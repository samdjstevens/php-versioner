<?php
namespace Spanky\Versioner\Console;

use RuntimeException;
use Spanky\Versioner\Versioner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

/**
 * Class AbstractVersionerCommand
 *
 * @package Spanky\Versioner
 */
abstract class AbstractVersionerCommand extends Command
{
    /**
     * @var \Spanky\Versioner\Versioner
     */
    protected $versioner;

    /**
     * Constructor.
     *
     * @param \Spanky\Versioner\Versioner $versioner
     */
    public function __construct(Versioner $versioner)
    {
        $this->versioner = $versioner;

        parent::__construct();
    }

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
