<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Codex;

use ArrayIterator;
use Countable;
use GreatMarketrealmCompanion\Services\Registry\RegistryItem;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

defined('ABSPATH') || exit;

/**
 * Collection of Codex entries.
 *
 * @implements IteratorAggregate<string, RegistryItem>
 *
 * @since 0.3.0
 */
final class CodexCollection implements Countable, IteratorAggregate
{
    /**
     * Entries indexed by their unique keys.
     *
     * @var array<string, RegistryItem>
     */
    private array $entries = [];

    /**
     * Create a Codex collection.
     *
     * @param iterable<RegistryItem> $entries
     */
    public function __construct(iterable $entries = [])
    {
        foreach ($entries as $entry) {
            $this->add($entry);
        }
    }

    /**
     * Add an entry to the collection.
     */
    private function add(RegistryItem $entry): void
    {
        $key = $entry->key();

        if ($this->has($key)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Codex collection already contains an entry with the key "%s".',
                    $key
                )
            );
        }

        $this->entries[$key] = $entry;
    }

    /**
     * Return all entries.
     *
     * @return array<string, RegistryItem>
     */
    public function all(): array
    {
        return $this->entries;
    }

    /**
     * Retrieve an entry by key.
     */
    public function get(string $key): ?RegistryItem
    {
        return $this->entries[$key] ?? null;
    }

    /**
     * Determine whether an entry exists.
     */
    public function has(string $key): bool
    {
        return isset($this->entries[$key]);
    }

    /**
     * Return the first entry.
     */
    public function first(): ?RegistryItem
    {
        if ($this->entries === []) {
            return null;
        }

        return reset($this->entries) ?: null;
    }

    /**
     * Return the final entry.
     */
    public function last(): ?RegistryItem
    {
        if ($this->entries === []) {
            return null;
        }

        return end($this->entries) ?: null;
    }

    /**
     * Determine whether the collection is empty.
     */
    public function isEmpty(): bool
    {
        return $this->entries === [];
    }

    /**
     * Return the number of entries.
     */
    public function count(): int
    {
        return count($this->entries);
    }

    /**
     * Allow the collection to be iterated.
     *
     * @return Traversable<string, RegistryItem>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->entries);
    }
}
