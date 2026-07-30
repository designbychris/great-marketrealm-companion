<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Codex;

use Countable;
use GreatMarketrealmCompanion\Services\Registry\Registry;
use GreatMarketrealmCompanion\Services\Registry\RegistryItem;

defined('ABSPATH') || exit;

/**
 * A section of the Codex.
 *
 * A section may receive entries from multiple registries.
 *
 * @since 0.3.0
 */
final class CodexSection implements Countable
{
    /**
     * Registries contributing to this section.
     *
     * @var array<int, Registry>
     */
    private array $registries = [];

    /**
     * Create a Codex section.
     */
    public function __construct(
        private string $key,
        private string $name
    ) {
    }

    /**
     * Return the section key.
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Return the section display name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Add a registry to this section.
     */
    public function addRegistry(Registry $registry): self
    {
        foreach ($this->registries as $registered) {
            if ($registered === $registry) {
                return $this;
            }
        }

        $this->registries[] = $registry;

        return $this;
    }

    /**
     * Return the registries contributing to the section.
     *
     * @return array<int, Registry>
     */
    public function registries(): array
    {
        return $this->registries;
    }

    /**
     * Return the section's collected entries.
     */
    public function entries(): CodexCollection
    {
        $entries = [];

        foreach ($this->registries as $registry) {
            foreach ($registry->all() as $entry) {
                $entries[] = $entry;
            }
        }

        return new CodexCollection($entries);
    }

    /**
     * Retrieve an entry from the section.
     */
    public function get(string $key): ?RegistryItem
    {
        return $this->entries()->get($key);
    }

    /**
     * Determine whether the section contains an entry.
     */
    public function has(string $key): bool
    {
        return $this->entries()->has($key);
    }

    /**
     * Determine whether the section is empty.
     */
    public function isEmpty(): bool
    {
        return $this->entries()->isEmpty();
    }

    /**
     * Return the number of entries in the section.
     */
    public function count(): int
    {
        return $this->entries()->count();
    }
}
