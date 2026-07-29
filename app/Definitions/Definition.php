<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Definitions;

use GreatMarketrealmCompanion\Services\Registry\Registry;
use GreatMarketrealmCompanion\Services\Registry\RegistryItem;

/**
 * Base class for fluent game-content definitions.
 */
abstract class Definition
{
    /**
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    private bool $registered = false;

    public function __construct(
        protected Registry $registry,
        protected string $key,
        protected string $name
    ) {
    }

    /**
     * Set an attribute on the definition.
     */
    protected function setAttribute(
        string $key,
        mixed $value
    ): static {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Add a value to an array-based attribute.
     *
     * Useful for traits, languages, proficiencies and similar values.
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

    /**
     * Register this definition with its registry.
     */
    final public function register(): void
    {
        if ($this->registered) {
            return;
        }

        $this->registry->set(
            new RegistryItem(
                key: $this->key,
                name: $this->name,
                attributes: $this->attributes,
            )
        );

        $this->registered = true;
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

    public function isRegistered(): bool
    {
        return $this->registered;
    }
}
