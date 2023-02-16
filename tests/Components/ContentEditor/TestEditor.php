<?php

namespace Tests\EnjoysCMS\Components\ContentEditor;

use EnjoysCMS\Core\Components\ContentEditor\ContentEditorInterface;

class TestEditor implements ContentEditorInterface
{

    private ?string $selector = null;

    public function __construct(private ?string $template = null)
    {

    }

    public function setSelector(string $selector): void
    {
        $this->selector = $selector;
    }

    public function getSelector(): string
    {
        if ($this->selector === null) {
            throw new \RuntimeException('Selector not set');
        }
        return $this->selector;
    }

    public function getEmbedCode(): string
    {
        return json_encode([
            $this->getSelector(),
            $this->template
        ]);
    }
}
