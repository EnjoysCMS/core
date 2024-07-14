<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Console\Command;


use Composer\InstalledVersions;
use EnjoysCMS\Core\Modules\Module;
use EnjoysCMS\Core\Modules\ModuleCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'post-install'
)]
final class PostInstall extends Command
{

    public function __construct(private readonly ModuleCollection $moduleCollection,string $name = null)
    {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->moduleCollection as $module) {
            $output->writeln([
                $module->moduleName,
            ]);
        }


        return 0;
    }
}
