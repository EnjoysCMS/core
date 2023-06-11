<?php

namespace Tests\EnjoysCMS\Components\ContentEditor;

use DI\Container;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use EnjoysCMS\Core\ContentEditor\ContentEditor;
use EnjoysCMS\Core\ContentEditor\NullEditor;
use PHPUnit\Framework\TestCase;

class ContentEditorTest extends TestCase
{

    private Container $container;

    protected function setUp(): void
    {
        $this->container = ContainerBuilder::buildDevContainer();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @dataProvider dataForTestWithConfig
     */
    public function testWithConfig($expect, $selector, $config): void
    {
        $contentEditor = new ContentEditor($this->container);
        $this->assertInstanceOf(NullEditor::class, $contentEditor->getEditor());
        $this->assertSame('', $contentEditor->getEmbedCode());

        $newContentEditor = $contentEditor->withConfig($config);
        $newContentEditor->setSelector($selector);
        $this->assertInstanceOf(NullEditor::class, $contentEditor->getEditor());
        $this->assertInstanceOf(array_key_first($config), $newContentEditor->getEditor());
        $this->assertSame($selector, $newContentEditor->getEditor()->getSelector());
        $this->assertSame($expect, $newContentEditor->getEmbedCode());
    }

    public function dataForTestWithConfig(): array
    {
        return [
            ['["#body","tpl"]', '#body', [
                TestEditor::class => 'tpl'
            ]],
            ['["#body","tpl"]', '#body', [
                TestEditor::class => [
                    'template' => 'tpl'
                ]
            ]],
        ];
    }


}
