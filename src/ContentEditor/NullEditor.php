<?php

namespace EnjoysCMS\Core\ContentEditor;

class NullEditor implements ContentEditorInterface
{

    public function setSelector(string $selector): void
    {
    }

    public function getSelector(): string
    {
        return '';
    }

    public function getEmbedCode(): string
    {
        return '';
    }
}
