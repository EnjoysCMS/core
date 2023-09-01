<?php

namespace EnjoysCMS\Core\Console\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrintModuleNameCommand extends Command
{

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Module name');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '',
            sprintf('<fg=yellow;options=bold>---%s---</>', implode(' ', $input->getArgument('name')))
        ]);

        return 0;
    }
}
