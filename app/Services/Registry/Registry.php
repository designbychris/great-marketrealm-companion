<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Registry;

/**
 * Base class for application registries.
 */
abstract class Registry
{
    /**
     * @var array<string, RegistryItem>
     */
    protected array $items = [];

    public function __construct()
    {
        $this->register();
    }

    /**
     * Register the default items for this registry.
     */
    abstract protected function register(): void;

    /**
     * Add a registry item.
     */
    protected function add(RegistryItem $item): void
    {
        $this->items[$item->key()] = $item;
    }

    /**
     * Register an item using its key, name and attributes.
     *
     * @param array<string, mixed> $attributes
     */
    protected function registerItem(
        string $key,
        string $name,
        array $attributes = []
    ): void {
        $this->add(
            new RegistryItem(
                key: $key,
                name: $name,
                attributes: $attributes,
            )
        );
    }

    /**
     * Register multiple registry items.
     *
     * @param array<int, RegistryItem> $items
     */
    protected function registerMany(array $items): void
    {
        foreach ($items as $item) {
            if (! $item instanceof RegistryItem) {
                throw new \InvalidArgumentException(
                    'Every item passed to registerMany() must be a RegistryItem.'
                );
            }

            $this->add($item);
        }
    }

    /**
     * Add or replace an item at runtime.
     */
    public function set(RegistryItem $item): void
    {
        $this->add($item);
    }

    /**
     * @return array<string, RegistryItem>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function get(string $key): ?RegistryItem
    {
        return $this->items[$key]
            ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists(
            $key,
            $this->items
        );
    }

    public function remove(string $key): void
    {
        unset($this->items[$key]);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Return registry items as select-friendly options.
     *
     * Example:
     *
     * [
     *     'fructan' => 'Fructan',
     *     'vegfolk' => 'Vegfolk',
     * ]
     *
     * @return array<string, string>
     */
    public function options(): array
    {
        $options = [];

        foreach ($this->items as $key => $item) {
            $options[$key] = $item->name();
        }

        return $options;
    }
}
