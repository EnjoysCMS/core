<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Blocks;

interface BlocksInterface
{
    public static function getBlockDefinitionFile(): string;

    public function view();
}
