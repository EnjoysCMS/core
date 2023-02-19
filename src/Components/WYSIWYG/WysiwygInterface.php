<?php

namespace EnjoysCMS\Core\Components\WYSIWYG;

/**
 * @deprecated
 */
interface WysiwygInterface
{
    public function getTwigTemplate(): string;

    public function setTwigTemplate(?string $twigTemplate): void;
}
