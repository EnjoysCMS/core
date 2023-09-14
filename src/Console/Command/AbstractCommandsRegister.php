<?php

namespace EnjoysCMS\Core\Console\Command;

use EnjoysCMS\Core\Console\Utils\CommandsManage;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommandsRegister extends Command
{

    protected string $moduleName;

    protected array $commands = [];

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        include __DIR__ . '/../../../../../../bootstrap.php';
        $commandManage = new CommandsManage();
        $registeredCommands = $commandManage->registerCommands($this->commands);
        $output->writeln('Register console commands:');
        if ($registeredCommands === []) {
            $output->writeln(' <fg=yellow>- skipped or nothing</></info>');
            return Command::SUCCESS;
        }
        foreach ($registeredCommands as $command) {
            $output->writeln(sprintf(' <fg=yellow>- %s</></info>', $command));
        }
        $commandManage->save();
        return Command::SUCCESS;
    }
}
