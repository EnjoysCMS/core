<?php

namespace EnjoysCMS\Core\Console\Command;


use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractAssetsInstallCommand extends Command
{

    protected string $cwd;
    protected array $command = [
        'yarn',
        'install',
    ];

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process($this->command, cwd: realpath($this->cwd));

        $output->writeln(sprintf("<info>Assets install [%s]</info>", $process->getWorkingDirectory()));

        $process->setTimeout(60);

        $process->run(
            function ($type, $buffer) use ($output) {
                $output->write((Process::ERR === $type) ? 'ERR:' . $buffer : $buffer);
            }
        );
        return Command::SUCCESS;
    }
}
