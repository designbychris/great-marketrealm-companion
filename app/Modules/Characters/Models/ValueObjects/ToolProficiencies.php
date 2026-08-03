<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Tool Proficiencies Value Object.
 *
 * Represents the immutable collection of tool
 * proficiencies possessed by a Character.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class ToolProficiencies
{
    /**
     * Create a Tool Proficiencies collection.
     *
     * @param array<int,ToolProficiency> $tools
     */
    private function __construct(
        private array $tools
    ) {
        $this->guardAgainstInvalidCollection(
            $tools
        );
    }

    /**
     * Create an empty collection.
     */
    public static function none(): self
    {
        return new self([]);
    }

    /**
     * Create a collection from Tool Proficiency values.
     */
    public static function fromTools(
        ToolProficiency ...$tools
    ): self {
        return new self(
            self::normaliseTools(
                $tools
            )
        );
    }

    /**
     * Create a collection from primitive identifiers.
     *
     * @param array<int,mixed> $tools
     */
    public static function fromStrings(
        array $tools
    ): self {
        $resolved = [];

        foreach ($tools as $tool) {
            if (! is_string($tool)) {
                throw new InvalidArgumentException(
                    'Character tool proficiency identifiers must be strings.'
                );
            }

            $resolved[] =
                ToolProficiency::fromString(
                    $tool
                );
        }

        return self::fromTools(
            ...$resolved
        );
    }

    /**
     * Return all Tool Proficiency values.
     *
     * @return array<int,ToolProficiency>
     */
    public function all(): array
    {
        return $this->tools;
    }

    /**
     * Return all canonical identifiers.
     *
     * @return array<int,string>
     */
    public function values(): array
    {
        return array_map(
            static fn (
                ToolProficiency $tool
            ): string => $tool->value(),
            $this->tools
        );
    }

    /**
     * Determine whether a proficiency is present.
     */
    public function has(
        ToolProficiency|string $tool
    ): bool {
        $tool = $tool instanceof ToolProficiency
            ? $tool
            : ToolProficiency::fromString($tool);

        foreach ($this->tools as $current) {
            if ($current->equals($tool)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a proficiency immutably.
     */
    public function add(
        ToolProficiency|string $tool
    ): self {
        $tool = $tool instanceof ToolProficiency
            ? $tool
            : ToolProficiency::fromString($tool);

        if ($this->has($tool)) {
            return $this;
        }

        return new self(
            self::normaliseTools([
                ...$this->tools,
                $tool,
            ])
        );
    }

    /**
     * Remove a proficiency immutably.
     */
    public function remove(
        ToolProficiency|string $tool
    ): self {
        $tool = $tool instanceof ToolProficiency
            ? $tool
            : ToolProficiency::fromString($tool);

        if (! $this->has($tool)) {
            return $this;
        }

        return new self(
            array_values(
                array_filter(
                    $this->tools,
                    static fn (
                        ToolProficiency $current
                    ): bool => ! $current->equals(
                        $tool
                    )
                )
            )
        );
    }

    /**
     * Merge another collection.
     */
    public function merge(
        self $other
    ): self {
        return new self(
            self::normaliseTools([
                ...$this->tools,
                ...$other->tools,
            ])
        );
    }

    /**
     * Return only concrete Artisan's Tools.
     *
     * @return array<int,ToolProficiency>
     */
    public function artisansTools(): array
    {
        return array_values(
            array_filter(
                $this->tools,
                static fn (
                    ToolProficiency $tool
                ): bool => $tool->isArtisansTool()
            )
        );
    }

    /**
     * Return only concrete Gaming Sets.
     *
     * @return array<int,ToolProficiency>
     */
    public function gamingSets(): array
    {
        return array_values(
            array_filter(
                $this->tools,
                static fn (
                    ToolProficiency $tool
                ): bool => $tool->isGamingSet()
            )
        );
    }

    /**
     * Determine whether the collection contains
     * a choice category awaiting resolution.
     */
    public function hasUnresolvedChoices(): bool
    {
        foreach ($this->tools as $tool) {
            if ($tool->isChoiceCategory()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return all unresolved choice categories.
     *
     * @return array<int,ToolProficiency>
     */
    public function unresolvedChoices(): array
    {
        return array_values(
            array_filter(
                $this->tools,
                static fn (
                    ToolProficiency $tool
                ): bool => $tool->isChoiceCategory()
            )
        );
    }

    /**
     * Determine whether the collection is empty.
     */
    public function isEmpty(): bool
    {
        return $this->tools === [];
    }

    /**
     * Return the number of proficiencies.
     */
    public function count(): int
    {
        return count(
            $this->tools
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
     * Remove duplicates and use canonical ordering.
     *
     * @param array<int,ToolProficiency> $tools
     *
     * @return array<int,ToolProficiency>
     */
    private static function normaliseTools(
        array $tools
    ): array {
        $byValue = [];

        foreach ($tools as $tool) {
            if (! $tool instanceof ToolProficiency) {
                throw new InvalidArgumentException(
                    'Tool proficiency collections may contain only ToolProficiency values.'
                );
            }

            $byValue[
                $tool->value()
            ] = $tool;
        }

        $ordered = [];

        foreach (
            ToolProficiency::all()
            as $supportedTool
        ) {
            $value = $supportedTool->value();

            if (isset($byValue[$value])) {
                $ordered[] = $byValue[$value];
            }
        }

        return $ordered;
    }

    /**
     * Guard against invalid collection members.
     *
     * @param array<int,ToolProficiency> $tools
     */
    private function guardAgainstInvalidCollection(
        array $tools
    ): void {
        foreach ($tools as $tool) {
            if (! $tool instanceof ToolProficiency) {
                throw new InvalidArgumentException(
                    'Tool proficiency collections may contain only ToolProficiency values.'
                );
            }
        }
    }
}
