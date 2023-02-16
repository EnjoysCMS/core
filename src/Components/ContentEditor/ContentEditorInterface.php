<?php

namespace EnjoysCMS\Core\Components\ContentEditor;

interface ContentEditorInterface
{

    public function setSelector(string $selector): void;

    public function getSelector(): string;

    public function getEmbedCode(): string;
}
