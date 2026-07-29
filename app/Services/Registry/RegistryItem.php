<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Registry;

/**
 * Represents a single immutable entry inside a registry.
 */
final class RegistryItem
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        protected string $key,
        protected string $name,
        protected array $attributes = [],
    ) {
        $this->key = trim($this->key);
        $this->name = trim($this->name);

        if ($this->key === '') {
            throw new \InvalidArgumentException(
                'A registry item key cannot be empty.'
            );
        }

        if ($this->name === '') {
            throw new \InvalidArgumentException(
                'A registry item name cannot be empty.'
            );
        }
    }

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function get(
        string $attribute,
        mixed $default = null
    ): mixed {
        return $this->attributes[$attribute]
            ?? $default;
    }

    public function has(string $attribute): bool
    {
        return array_key_exists(
            $attribute,
            $this->attributes
        );
    }

    /**
     * Return the item as a plain array.
     *
     * @return array{
     *     key: string,
     *     name: string,
     *     attributes: array<string, mixed>
     * }
     */
    public function toArray(): array
    {
        return [
            'key'        => $this->key,
            'name'       => $this->name,
            'attributes' => $this->attributes,
        ];
    }
}
