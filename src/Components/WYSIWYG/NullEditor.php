<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\WYSIWYG;

/**
 * @deprecated
 */
final class NullEditor implements WysiwygInterface
{
    public function getTwigTemplate(): string
    {
        return __DIR__ . '/nulleditor.twig';
    }

    public function setTwigTemplate(?string $twigTemplate): void
    {
    }
}
