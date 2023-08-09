<?php

namespace Tests\EnjoysCMS\Components\ContentEditor;

use EnjoysCMS\Core\ContentEditor\EditorConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class EditorConfigTest
{

    /**
     * @dataProvider dataForTestParseConfig
     */
    public function testParseConfig($expect, $input)
    {
        $config = new EditorConfig($input);
        $this->assertSame($expect['editorClassNameOrAlias'], $config->getEditorClassNameOrAlias());
        $this->assertSame($expect['params'] ?? [], $config->getParams());
      // var_dump($config->getTemplate('@'));
    }

    public function dataForTestParseConfig(): array
    {
        return [
            [
                [
                    'editorClassNameOrAlias' => '\App\EditorClassName',
                    'params' => [],
                ],
                Yaml::parse(
                    <<<YAML
\App\EditorClassName
YAML
                )
            ],
            [
                [
                    'editorClassNameOrAlias' => '\App\EditorClassName2',
                    'params' => [],
                ],
                Yaml::parse(
                    <<<YAML
\App\EditorClassName2:
YAML
                )
            ],
            [
                [
                    'editorClassNameOrAlias' => '\App\EditorClassName3',
                    'params' => [
                        'template' => 'temlate1.tpl'
                    ],
                ],
                Yaml::parse(
                    <<<YAML
\App\EditorClassName3: temlate1.tpl
YAML
                )
            ],
            [
                [
                    'editorClassNameOrAlias' => '\App\EditorClassName3',
                    'params' => [
                        'crud' => 'temlate1.tpl'
                    ],
                ],
                Yaml::parse(
                    <<<YAML
\App\EditorClassName3:
    crud: temlate1.tpl
YAML
                )
            ],
            [
                [
                    'editorClassNameOrAlias' => '\App\EditorClassName3',
                    'params' => [
                        'template' => 'template1.tpl',
                        'arg1' => ['v1', 'v2'],
                        'arg2' => 'v3'
                    ],
                ],
                Yaml::parse(
                    <<<YAML
\App\EditorClassName3:
    template: template1.tpl
    arg1:
        - v1
        - v2
    arg2: v3
YAML
                )
            ],
        ];
    }
}
