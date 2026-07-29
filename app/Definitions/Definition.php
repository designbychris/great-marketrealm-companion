<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Definitions;

use GreatMarketrealmCompanion\Services\Registry\RegistryItem;

/**
 * Base class for fluent game-content definitions.
 *
 * Definitions describe game content but know nothing about
 * how or where that content is stored.
 */
abstract class Definition
{
    /**
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    public function __construct(
        protected string $key,
        protected string $name
    ) {
    }

    /**
     * Set an attribute.
     */
    protected function setAttribute(
        string $key,
        mixed $value
    ): static {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Append a value to an array attribute.
     */
    protected function addToAttribute(
        string $key,
        mixed $value
    ): static {
        $values = $this->attributes[$key] ?? [];

        if (! is_array($values)) {
            $values = [$values];
        }

        $values[] = $value;

        $this->attributes[$key] = $values;

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Common Definition Attributes
    |--------------------------------------------------------------------------
    */

    public function description(
        string $description
    ): static {
        return $this->setAttribute(
            'description',
            $description
        );
    }

    public function icon(
        string $icon
    ): static {
        return $this->setAttribute(
            'icon',
            $icon
        );
    }

    public function portrait(
        string $portrait
    ): static {
        return $this->setAttribute(
            'portrait',
            $portrait
        );
    }

    public function colour(
        string $colour
    ): static {
        return $this->setAttribute(
            'colour',
            $colour
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Information
    |--------------------------------------------------------------------------
    */

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array<string,mixed>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * Convert the definition into a RegistryItem.
     */
    public function toRegistryItem(): RegistryItem
    {
        return new RegistryItem(
            key: $this->key,
            name: $this->name,
            attributes: $this->attributes,
        );
    }

    /**
     * Convert the definition into an array.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'attributes' => $this->attributes,
        ];
    }
}
