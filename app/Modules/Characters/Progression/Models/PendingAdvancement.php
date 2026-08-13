<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;

defined('ABSPATH') || exit;

final class PendingAdvancement
{
    private const SCHEMA_VERSION = 1;

    /**
     * @param array<string,array<int,string>> $choices
     */
    private function __construct(
        private CharacterId $characterId,
        private int $fromLevel,
        private int $targetLevel,
        private array $choices = []
    ) {
    }

    public static function begin(
        CharacterId $characterId,
        int $fromLevel,
        int $targetLevel
    ): self {
        return new self(
            $characterId,
            $fromLevel,
            $targetLevel
        );
    }

    /**
     * @param array<string,mixed> $stored
     */
    public static function fromArray(
        CharacterId $characterId,
        array $stored
    ): ?self {
        if (
            (int) ($stored['schema_version'] ?? 0)
                !== self::SCHEMA_VERSION
            || (string) ($stored['character_id'] ?? '')
                !== $characterId->value()
        ) {
            return null;
        }

        $fromLevel = (int) ($stored['from_level'] ?? 0);
        $targetLevel = (int) ($stored['target_level'] ?? 0);
        $rawChoices = $stored['choices'] ?? [];

        if (
            $fromLevel < 1
            || $targetLevel !== $fromLevel + 1
            || ! is_array($rawChoices)
        ) {
            return null;
        }

        $choices = [];

        foreach ($rawChoices as $key => $values) {
            if (
                ! is_string($key)
                || ! is_array($values)
            ) {
                continue;
            }

            $choices[sanitize_key($key)] =
                array_values(
                    array_filter(
                        array_map(
                            static fn (mixed $value): string =>
                                sanitize_key((string) $value),
                            $values
                        )
                    )
                );
        }

        return new self(
            $characterId,
            $fromLevel,
            $targetLevel,
            $choices
        );
    }

    public function matches(
        int $fromLevel,
        int $targetLevel
    ): bool {
        return $this->fromLevel === $fromLevel
            && $this->targetLevel === $targetLevel;
    }

    public function characterId(): CharacterId
    {
        return $this->characterId;
    }

    public function fromLevel(): int
    {
        return $this->fromLevel;
    }

    public function targetLevel(): int
    {
        return $this->targetLevel;
    }

    /**
     * @return array<string,array<int,string>>
     */
    public function choices(): array
    {
        return $this->choices;
    }

    /**
     * @param array<int,string> $selections
     */
    public function recordChoice(
        string $choiceKey,
        array $selections
    ): void {
        $this->choices[sanitize_key($choiceKey)] =
            array_values(
                array_unique(
                    array_filter(
                        array_map(
                            static fn (mixed $value): string =>
                                sanitize_key((string) $value),
                            $selections
                        )
                    )
                )
            );
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'schema_version' => self::SCHEMA_VERSION,
            'character_id' =>
                $this->characterId->value(),
            'from_level' => $this->fromLevel,
            'target_level' => $this->targetLevel,
            'choices' => $this->choices,
        ];
    }
}
