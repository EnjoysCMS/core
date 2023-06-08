<?php

namespace EnjoysCMS\Core\Block;

class BlockOptions
{
    /**
     * @var array<string, array{value: mixed}>
     */
    private array $options = [];

    public static function createFromArray(array $data): BlockOptions
    {
        return new self($data);
    }


    public function __construct(array $data = [])
    {
        $this->populate($data);
    }


    public function all(): array
    {
        return $this->options;
    }

    public function getOption(string $key): ?array
    {
        return $this->options[$key] ?? null;
    }

    public function getValue(string $key): mixed
    {
        return $this->options[$key]['value'];
    }

    private function populate(array $data): void
    {
        if ($data === []) {
            return;
        }

        foreach ($data as $key => $value) {
            if (is_int($key)) {
                continue;
            }

            $this->options[$key] = $this->normalizeValue($value);
        }
    }

    private function normalizeValue(mixed $value): mixed
    {
        if (is_scalar($value) || is_null($value)) {
            return [
                'value' => $value
            ];
        }

        if (is_array($value)) {
            if (!array_key_exists('value', $value)) {
                return [
                    'value' => $value
                ];
            }

        }
        return $value;
    }


}
