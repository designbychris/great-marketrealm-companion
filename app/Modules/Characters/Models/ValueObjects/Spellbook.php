<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

defined('ABSPATH') || exit;

final class Spellbook
{
    /**
     * @param array<int,string> $spells
     * @param array<int,string> $cantrips
     */
    private function __construct(
        private array $spells,
        private array $cantrips
    ) {
        $this->spells = $this->normalise($spells);
        $this->cantrips = $this->normalise($cantrips);
    }

    public static function empty(): self
    {
        return new self([], []);
    }

    /** @param array<string,mixed> $stored */
    public static function fromArray(array $stored): self
    {
        return new self(
            is_array($stored['spells'] ?? null)
                ? $stored['spells']
                : [],
            is_array($stored['cantrips'] ?? null)
                ? $stored['cantrips']
                : []
        );
    }

    /**
     * @param array<int,string> $spells
     * @param array<int,string> $cantrips
     */
    public function learn(
        array $spells = [],
        array $cantrips = []
    ): self {
        return new self(
            array_merge($this->spells, $spells),
            array_merge($this->cantrips, $cantrips)
        );
    }

    /** @return array<int,string> */
    public function spells(): array
    {
        return $this->spells;
    }

    /** @return array<int,string> */
    public function cantrips(): array
    {
        return $this->cantrips;
    }

    public function knows(string $abilityId): bool
    {
        $id = sanitize_key($abilityId);

        return in_array($id, $this->spells, true)
            || in_array($id, $this->cantrips, true);
    }

    /** @return array{spells:array<int,string>,cantrips:array<int,string>} */
    public function toArray(): array
    {
        return [
            'spells' => $this->spells,
            'cantrips' => $this->cantrips,
        ];
    }

    /** @param array<int,mixed> $values @return array<int,string> */
    private function normalise(array $values): array
    {
        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn (mixed $value): string =>
                            sanitize_key((string) $value),
                        $values
                    )
                )
            )
        );
    }
}
