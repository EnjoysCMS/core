<?php

namespace EnjoysCMS\Core\Components\ContentEditor;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ContentEditor
{
    private EditorConfig $config;
    private ContentEditorInterface $editor;
    private ?LoggerInterface $logger;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(
        private Container $container,
        array|string|null|EditorConfig $config = null,
        ?LoggerInterface $logger = null,
    ) {
        $this->config = ($config instanceof EditorConfig) ? $config : new EditorConfig($config);
        $this->logger = $logger ?? new NullLogger();

        $this->editor = $this->initEditor();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function withConfig(array|string|null|EditorConfig $config): ContentEditor
    {
        return new ContentEditor($this->container, $config, $this->logger);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    private function initEditor(): ContentEditorInterface
    {
        try {
            return $this->container->make(
                $this->config->getEditorClassNameOrAlias(),
                $this->config->getParams()
            );
        } catch (DependencyException|NotFoundException $e) {
            $this->logger?->error($e->getMessage());
        }

        return $this->container->make(NullEditor::class, []);
    }

    public function getEditor(): ContentEditorInterface
    {
        return $this->editor;
    }


    public function setSelector(string $selector): ContentEditor
    {
        $this->editor->setSelector($selector);
        return $this;
    }

    public function getEmbedCode(): string
    {
        return $this->editor->getEmbedCode();
    }
}
