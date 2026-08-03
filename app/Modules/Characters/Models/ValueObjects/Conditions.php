<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Conditions Value Object.
 *
 * Represents the immutable collection of conditions
 * currently affecting a Character.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class Conditions
{
    /**
     * Create a Conditions collection.
     *
     * @param array<int,Condition> $conditions
     */
    private function __construct(
        private array $conditions
    ) {
        $this->guardAgainstInvalidCollection(
            $conditions
        );
    }

    /**
     * Create an empty Conditions collection.
     */
    public static function none(): self
    {
        return new self([]);
    }

    /**
     * Create a collection from Condition values.
     */
    public static function fromConditions(
        Condition ...$conditions
    ): self {
        return new self(
            self::normaliseConditions(
                $conditions
            )
        );
    }

    /**
     * Create a collection from primitive identifiers.
     *
     * @param array<int,mixed> $conditions
     */
    public static function fromStrings(
        array $conditions
    ): self {
        $resolved = [];

        foreach ($conditions as $condition) {
            if (! is_string($condition)) {
                throw new InvalidArgumentException(
                    'Character condition identifiers must be strings.'
                );
            }

            $resolved[] = Condition::fromString(
                $condition
            );
        }

        return self::fromConditions(
            ...$resolved
        );
    }

    /**
     * Return all current conditions.
     *
     * @return array<int,Condition>
     */
    public function all(): array
    {
        return $this->conditions;
    }

    /**
     * Return all canonical condition identifiers.
     *
     * @return array<int,string>
     */
    public function values(): array
    {
        return array_map(
            static fn (
                Condition $condition
            ): string => $condition->value(),
            $this->conditions
        );
    }

    /**
     * Determine whether the Character has a condition.
     */
    public function has(
        Condition|string $condition
    ): bool {
        $condition = $condition instanceof Condition
            ? $condition
            : Condition::fromString($condition);

        foreach ($this->conditions as $current) {
            if ($current->equals($condition)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a condition immutably.
     */
    public function add(
        Condition|string $condition
    ): self {
        $condition = $condition instanceof Condition
            ? $condition
            : Condition::fromString($condition);

        if ($this->has($condition)) {
            return $this;
        }

        return new self(
            self::normaliseConditions([
                ...$this->conditions,
                $condition,
            ])
        );
    }

    /**
     * Remove a condition immutably.
     */
    public function remove(
        Condition|string $condition
    ): self {
        $condition = $condition instanceof Condition
            ? $condition
            : Condition::fromString($condition);

        if (! $this->has($condition)) {
            return $this;
        }

        return new self(
            array_values(
                array_filter(
                    $this->conditions,
                    static fn (
                        Condition $current
                    ): bool => ! $current->equals(
                        $condition
                    )
                )
            )
        );
    }

    /**
     * Merge another Conditions collection.
     */
    public function merge(
        self $other
    ): self {
        return new self(
            self::normaliseConditions([
                ...$this->conditions,
                ...$other->conditions,
            ])
        );
    }

    /**
     * Determine whether no conditions are active.
     */
    public function isEmpty(): bool
    {
        return $this->conditions === [];
    }

    /**
     * Return the number of active conditions.
     */
    public function count(): int
    {
        return count(
            $this->conditions
        );
    }

    /**
     * Determine whether this collection equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->values()
            === $other->values();
    }

    /**
     * Remove duplicates and return canonical ordering.
     *
     * @param array<int,Condition> $conditions
     *
     * @return array<int,Condition>
     */
    private static function normaliseConditions(
        array $conditions
    ): array {
        $byValue = [];

        foreach ($conditions as $condition) {
            if (! $condition instanceof Condition) {
                throw new InvalidArgumentException(
                    'Conditions collections may contain only Condition values.'
                );
            }

            $byValue[
                $condition->value()
            ] = $condition;
        }

        $ordered = [];

        foreach (Condition::all() as $supported) {
            if (isset(
                $byValue[$supported->value()]
            )) {
                $ordered[] = $byValue[
                    $supported->value()
                ];
            }
        }

        return $ordered;
    }

    /**
     * Guard against invalid collection members.
     *
     * @param array<int,Condition> $conditions
     */
    private function guardAgainstInvalidCollection(
        array $conditions
    ): void {
        foreach ($conditions as $condition) {
            if (! $condition instanceof Condition) {
                throw new InvalidArgumentException(
                    'Conditions collections may contain only Condition values.'
                );
            }
        }
    }
}
