<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\WYSIWYG;


final class WysiwygConfig
{
    private string|null $editorName = null;
    private string|array|null $params = null;

    public function __construct(array|string|null $config)
    {

        if (is_string($config)){
            $this->editorName = $config;
        }

        if (is_array($config)){
            $key = array_key_last($config);
            if (!is_string($key)){
                throw new \InvalidArgumentException('Incorrect editor name set');
            }

            $this->editorName = $key;

            $this->params = $config[$key] ?? null;

        }

    }

    public function getEditorName(): ?string
    {
        return $this->editorName;
    }

    public function getTemplate(?string $key = null): ?string
    {
        if ($key === null){
            if (is_array($this->params)){
                return null;
            }
            return $this->params;
        }
        return $this->params[$key] ?? null;
    }

    public function getParams(): string|array|null
    {
        return $this->params;
    }
}
