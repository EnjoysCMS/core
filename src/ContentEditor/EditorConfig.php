<?php

namespace EnjoysCMS\Core\ContentEditor;

class EditorConfig
{

    private string $editorClassNameOrAlias = NullEditor::class;
    private array $params = [];

    /**
     * @param array<string, mixed>|string|null $config
     */
    public function __construct(array|string|null $config)
    {
        if (is_string($config)) {
            $this->editorClassNameOrAlias = $config;
        }

        if (is_array($config)) {
            $key = array_key_last($config);

            if (!is_string($key)) {
                throw new \InvalidArgumentException('Incorrect editor name set');
            }

            $this->editorClassNameOrAlias = $key;
            $this->params = $this->parseParams($config[$key] ?? []);
        }
    }

    public function getEditorClassNameOrAlias(): string
    {
        return $this->editorClassNameOrAlias;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    private function parseParams(array|string $param): array
    {
        if (is_string($param)){
            return [
              'template' => $param
            ];
        }
        return $param;
    }
}
