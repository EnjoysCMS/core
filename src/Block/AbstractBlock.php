<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

use EnjoysCMS\Core\Entities;

abstract class AbstractBlock implements BlockInterface
{

    private ?Entities\Block $entity;

    public static function getMeta(): array
    {
        return [];
    }

    final public function getBlockOptions(): Options
    {
        return  Options::createFromArray($this->entity->getOptions() ?? []);
    }

    final public function setEntity(Entities\Block $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    final public function getEntity(): ?Entities\Block
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
