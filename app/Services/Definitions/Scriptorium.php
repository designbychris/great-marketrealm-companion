<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions;

use Countable;
use GreatMarketrealmCompanion\Definitions\Definition;
use GreatMarketrealmCompanion\Services\Definitions\Builders\ClassBuilder;
use GreatMarketrealmCompanion\Services\Definitions\Builders\RaceBuilder;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * The Scriptorium.
 *
 * Collects and authors game-content definitions.
 *
 * Each Scriptorium instance represents a single authoring session,
 * chapter, registry, sourcebook or expansion pack.
 *
 * @since 0.3.0
 */
final class Scriptorium implements Countable
{
    /**
     * Definitions indexed by their unique keys.
     *
     * @var array<string, Definition>
     */
    private array $definitions = [];

    /**
     * Create a new Scriptorium.
     */
    public function __construct(
        private Definitions $definitionService
    ) {
    }

    /**
     * Begin authoring a race.
     */
    public function race(
        string $key,
        string $name
    ): RaceBuilder {
        $definition = $this->definitionService->race(
            key: $key,
            name: $name
        );

        $this->add($definition);

        return new RaceBuilder(
            race: $definition,
            scriptorium: $this
        );
    }

    /**
     * Begin authoring a character class.
     */
    public function characterClass(
        string $key,
        string $name
    ): ClassBuilder {
        $definition = $this->definitionService->characterClass(
            key: $key,
            name: $name
        );

        $this->add($definition);

        return new ClassBuilder(
            characterClass: $definition,
            scriptorium: $this
        );
    }

    /**
     * Add an existing definition to the Scriptorium.
     */
    public function add(Definition $definition): self
    {
        $key = $definition->key();

        if ($this->has($key)) {
            throw new InvalidArgumentException(
                sprintf(
                    'A definition with the key "%s" already exists in this Scriptorium.',
                    $key
                )
            );
        }

        $this->definitions[$key] = $definition;

        return $this;
    }

    /**
     * Determine whether the Scriptorium contains a definition.
     */
    public function has(string $key): bool
    {
        return isset(
            $this->definitions[$key]
        );
    }

    /**
     * Retrieve a definition by key.
     */
    public function get(string $key): ?Definition
    {
        return $this->definitions[$key] ?? null;
    }

    /**
     * Return all authored definitions.
     *
     * @return array<int, Definition>
     */
    public function definitions(): array
    {
        return array_values(
            $this->definitions
        );
    }

    /**
     * Determine whether the Scriptorium contains no definitions.
     */
    public function isEmpty(): bool
    {
        return $this->definitions === [];
    }

    /**
     * Return the number of authored definitions.
     */
    public function count(): int
    {
        return count(
            $this->definitions
        );
    }
}
