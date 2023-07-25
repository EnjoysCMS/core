<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

use EnjoysCMS\Core\Block\Entity\Block;

abstract class AbstractBlock implements BlockInterface
{

    private ?Block $entity;

    final public function getBlockOptions(): Options
    {
        return $this->entity?->getOptions() ?? new Options();
    }

    final public function setEntity(Block $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    final public function getEntity(): ?Block
    {
        return $this->entity;
    }

    public function preRemove(): void
    {
    }

    public function postEdit(): void
    {
    }

    public function postClone(): void
    {
    }


}
