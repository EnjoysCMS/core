<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Blocks;

interface BlockInterface
{
    public function view(): string;
}
