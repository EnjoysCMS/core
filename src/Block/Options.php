<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements  ArrayAccess<string, array{value: mixed}>
 * @implements  IteratorAggregate<string, array{value: mixed}>
 */
class Options implements ArrayAccess, IteratorAggregate, JsonSerializable
{
    /**
     * @var array<string, array{value: mixed}>
     */
    private array $options = [];

    public static function createFromArray(array $data): Options
    {
        return new self($data);
    }


    public function __construct(array $data = [])
    {
        $this->populate($data);
    }


    /**
     * @return array<string, array{value: mixed}>
     */
    public function toArray(): array
    {
        return $this->options;
    }

    public function getOption(string $key): ?array
    {
        return $this->options[$key] ?? null;
    }

    public function getValue(string $key): mixed
    {
        return $this->options[$key]['value'] ?? null;
    }

    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }

    public function setValue(string $key, mixed $value): void
    {
        if ($this->hasOption($key)) {
            $this->options[$key]['value'] = $value;
        }
    }

    /**
     * @param array<string, mixed> $options
     * @return void
     */
    public function setValues(array $options): void
    {
        foreach ($options as $key => $value) {
            $this->setValue($key, $value);
        }
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


    /**
     * @param mixed $value
     * @return array{value: mixed}
     */
    private function normalizeValue(mixed $value): array
    {
        if (is_array($value)) {
            if (array_key_exists('value', $value)) {
                /** @var array{value: mixed} $value */
                return $value;
            }
        }
        return [
            'value' => $value
        ];
    }


    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->options[$offset]);
    }

    /**
     * @param string $offset
     * @return array{value: mixed}|null
     */
    public function offsetGet($offset): ?array
    {
        return $this->options[$offset] ?? null;
    }

    /**
     * @param string $offset
     * @param array{value: mixed} $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->options[$offset] = $value;
    }

    /**
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->options[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->options);
    }


    public function jsonSerialize(): array
    {
        return $this->options;
    }
}
