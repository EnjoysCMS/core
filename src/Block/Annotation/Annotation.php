<?php

namespace EnjoysCMS\Core\Block\Annotation;

use EnjoysCMS\Core\Block\Options;

interface Annotation
{
    public function getOptions(): Options;

    public function getName(): string;

    public function getClassName(): string;
}
