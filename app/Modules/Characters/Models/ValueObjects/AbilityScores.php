<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

defined('ABSPATH') || exit;

/**
 * Immutable collection of character ability scores.
 *
 * Groups the six core ability scores used throughout
 * the Great Marketrealm character system.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class AbilityScores
{
    /**
     * Create an ability-score collection.
     */
    private function __construct(
        private readonly AbilityScore $strength,
        private readonly AbilityScore $dexterity,
        private readonly AbilityScore $constitution,
        private readonly AbilityScore $intelligence,
        private readonly AbilityScore $wisdom,
        private readonly AbilityScore $charisma,
    ) {
    }

    /**
     * Create a collection where every ability score is average.
     */
    public static function average(): self
    {
        return new self(
            strength: AbilityScore::average(),
            dexterity: AbilityScore::average(),
            constitution: AbilityScore::average(),
            intelligence: AbilityScore::average(),
            wisdom: AbilityScore::average(),
            charisma: AbilityScore::average(),
        );
    }

    /**
     * Create a collection from six ability scores.
     */
    public static function fromScores(
        AbilityScore $strength,
        AbilityScore $dexterity,
        AbilityScore $constitution,
        AbilityScore $intelligence,
        AbilityScore $wisdom,
        AbilityScore $charisma,
    ): self {
        return new self(
            strength: $strength,
            dexterity: $dexterity,
            constitution: $constitution,
            intelligence: $intelligence,
            wisdom: $wisdom,
            charisma: $charisma,
        );
    }

    /**
     * Return the Strength score.
     */
    public function strength(): AbilityScore
    {
        return $this->strength;
    }

    /**
     * Return the Dexterity score.
     */
    public function dexterity(): AbilityScore
    {
        return $this->dexterity;
    }

    /**
     * Return the Constitution score.
     */
    public function constitution(): AbilityScore
    {
        return $this->constitution;
    }

    /**
     * Return the Intelligence score.
     */
    public function intelligence(): AbilityScore
    {
        return $this->intelligence;
    }

    /**
     * Return the Wisdom score.
     */
    public function wisdom(): AbilityScore
    {
        return $this->wisdom;
    }

    /**
     * Return the Charisma score.
     */
    public function charisma(): AbilityScore
    {
        return $this->charisma;
    }

    /**
     * Return a new collection with an updated Strength score.
     */
    public function withStrength(
        AbilityScore $strength
    ): self {
        return new self(
            strength: $strength,
            dexterity: $this->dexterity,
            constitution: $this->constitution,
            intelligence: $this->intelligence,
            wisdom: $this->wisdom,
            charisma: $this->charisma,
        );
    }

    /**
     * Return a new collection with an updated Dexterity score.
     */
    public function withDexterity(
        AbilityScore $dexterity
    ): self {
        return new self(
            strength: $this->strength,
            dexterity: $dexterity,
            constitution: $this->constitution,
            intelligence: $this->intelligence,
            wisdom: $this->wisdom,
            charisma: $this->charisma,
        );
    }

    /**
     * Return a new collection with an updated Constitution score.
     */
    public function withConstitution(
        AbilityScore $constitution
    ): self {
        return new self(
            strength: $this->strength,
            dexterity: $this->dexterity,
            constitution: $constitution,
            intelligence: $this->intelligence,
            wisdom: $this->wisdom,
            charisma: $this->charisma,
        );
    }

    /**
     * Return a new collection with an updated Intelligence score.
     */
    public function withIntelligence(
        AbilityScore $intelligence
    ): self {
        return new self(
            strength: $this->strength,
            dexterity: $this->dexterity,
            constitution: $this->constitution,
            intelligence: $intelligence,
            wisdom: $this->wisdom,
            charisma: $this->charisma,
        );
    }

    /**
     * Return a new collection with an updated Wisdom score.
     */
    public function withWisdom(
        AbilityScore $wisdom
    ): self {
        return new self(
            strength: $this->strength,
            dexterity: $this->dexterity,
            constitution: $this->constitution,
            intelligence: $this->intelligence,
            wisdom: $wisdom,
            charisma: $this->charisma,
        );
    }

    /**
     * Return a new collection with an updated Charisma score.
     */
    public function withCharisma(
        AbilityScore $charisma
    ): self {
        return new self(
            strength: $this->strength,
            dexterity: $this->dexterity,
            constitution: $this->constitution,
            intelligence: $this->intelligence,
            wisdom: $this->wisdom,
            charisma: $charisma,
        );
    }

    /**
     * Determine whether two ability-score collections are equal.
     */
    public function equals(self $other): bool
    {
        return $this->strength->equals(
            $other->strength
        )
            && $this->dexterity->equals(
                $other->dexterity
            )
            && $this->constitution->equals(
                $other->constitution
            )
            && $this->intelligence->equals(
                $other->intelligence
            )
            && $this->wisdom->equals(
                $other->wisdom
            )
            && $this->charisma->equals(
                $other->charisma
            );
    }

    /**
     * Return all ability scores keyed by name.
     *
     * @return array<string, AbilityScore>
     */
    public function all(): array
    {
        return [
            'strength' => $this->strength,
            'dexterity' => $this->dexterity,
            'constitution' => $this->constitution,
            'intelligence' => $this->intelligence,
            'wisdom' => $this->wisdom,
            'charisma' => $this->charisma,
        ];
    }
}
