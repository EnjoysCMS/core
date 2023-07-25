<?php

namespace EnjoysCMS\Core\Block;

use EnjoysCMS\Core\Block\Entity\Widget;

abstract class AbstractWidget
{

    private ?Widget $entity;

    abstract public function view();

    final public function getBlockOptions(): Options
    {
        return $this->entity?->getOptions() ?? new Options();
    }

    final public function setEntity(Widget $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    final public function getEntity(): ?Widget
    {
        return $this->entity;
    }

}
