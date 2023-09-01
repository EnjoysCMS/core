<?php

namespace EnjoysCMS\Core\Console\Command;

use Enjoys\Config\Config;
use EnjoysCMS\Core\Console\Utils\CommandsManage;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterCommands extends Command
{

    private array $commands = [
        self::class => null
    ];

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $container = include __DIR__ . '/../../../../../../bootstrap.php';
        $commandManage = new CommandsManage(config: $container->get(Config::class));
        $registeredCommands = $commandManage->registerCommands($this->commands);
        $changed = false;
        foreach ($registeredCommands as $command) {
            $output->writeln(sprintf("<info>Register command: <options=bold>%s</></info>", $command));
            $changed = true;
        }
        if ($changed){
            $commandManage->save();
        }
        return 0;
    }
}
