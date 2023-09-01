<?php

namespace EnjoysCMS\Core\Console\Command;

use Enjoys\Config\Config;
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
        if ($this->commands === []){
            return Command::SUCCESS;
        }
        $container = include __DIR__ . '/../../../../../../bootstrap.php';
        $commandManage = new CommandsManage(config: $container->get(Config::class));
        $registeredCommands = $commandManage->registerCommands($this->commands);
        $changed = false;
        $output->writeln(sprintf("<info>Register console commands [%s]</info>", $this->moduleName));
        foreach ($registeredCommands as $command) {
            $output->writeln(sprintf(' <options=bold>- %s</></info>', $command));
            $changed = true;
        }
        if ($changed) {
            $commandManage->save();
        }
        return Command::SUCCESS;
    }
}
