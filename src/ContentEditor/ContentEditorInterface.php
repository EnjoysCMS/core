<?php

namespace EnjoysCMS\Core\ContentEditor;

interface ContentEditorInterface
{

    public function setSelector(string $selector): void;

    public function getSelector(): string;

    public function getEmbedCode(): string;
}
