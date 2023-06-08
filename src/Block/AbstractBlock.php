<?php

namespace EnjoysCMS\Core\Block;

abstract class AbstractBlock implements BlockInterface
{

    private ?\EnjoysCMS\Core\Block\Entity\Block $entity;

    final public function getBlockOptions(): BlockOptions
    {
        return $this->entity?->getOptions() ?? new BlockOptions();
    }

    final public function setEntity(\EnjoysCMS\Core\Block\Entity\Block $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    final public function getEntity(): ?Entity\Block
    {
        return $this->entity;
    }

    public function preRemove()
    {
    }

    public function postEdit()
    {
    }

    public function postClone()
    {
    }


}
