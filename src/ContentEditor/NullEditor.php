<?php

namespace EnjoysCMS\Core\ContentEditor;

class NullEditor implements ContentEditorInterface
{

    #[\Override]
    public function setSelector(string $selector): void
    {
    }

    #[\Override]
    public function getSelector(): string
    {
        return '';
    }

    #[\Override]
    public function getEmbedCode(): string
    {
        return '';
    }
}
