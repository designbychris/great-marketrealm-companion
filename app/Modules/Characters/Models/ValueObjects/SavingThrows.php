<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Saving Throws Value Object.
 *
 * Represents all six Character saving throws.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class SavingThrows
{
    /**
     * Supported ability identifiers.
     *
     * @var string[]
     */
    private const ABILITIES = [
        'strength',
        'dexterity',
        'constitution',
        'intelligence',
        'wisdom',
        'charisma',
    ];

    /**
     * Create the Saving Throws collection.
     */
    private function __construct(
        private SavingThrow $strength,
        private SavingThrow $dexterity,
        private SavingThrow $constitution,
        private SavingThrow $intelligence,
        private SavingThrow $wisdom,
        private SavingThrow $charisma
    ) {
    }

    /**
     * Build Saving Throws from Character values.
     *
     * @param string[] $proficientAbilities
     */
    public static function fromAbilityScores(
        AbilityScores $abilityScores,
        ProficiencyBonus $proficiencyBonus,
        array $proficientAbilities = []
    ): self {
        $proficiencies = self::normaliseProficiencies(
            $proficientAbilities
        );

        return new self(
            strength: SavingThrow::fromAbility(
                $abilityScores->strength(),
                $proficiencyBonus,
                in_array(
                    'strength',
                    $proficiencies,
                    true
                )
            ),
            dexterity: SavingThrow::fromAbility(
                $abilityScores->dexterity(),
                $proficiencyBonus,
                in_array(
                    'dexterity',
                    $proficiencies,
                    true
                )
            ),
            constitution: SavingThrow::fromAbility(
                $abilityScores->constitution(),
                $proficiencyBonus,
                in_array(
                    'constitution',
                    $proficiencies,
                    true
                )
            ),
            intelligence: SavingThrow::fromAbility(
                $abilityScores->intelligence(),
                $proficiencyBonus,
                in_array(
                    'intelligence',
                    $proficiencies,
                    true
                )
            ),
            wisdom: SavingThrow::fromAbility(
                $abilityScores->wisdom(),
                $proficiencyBonus,
                in_array(
                    'wisdom',
                    $proficiencies,
                    true
                )
            ),
            charisma: SavingThrow::fromAbility(
                $abilityScores->charisma(),
                $proficiencyBonus,
                in_array(
                    'charisma',
                    $proficiencies,
                    true
                )
            )
        );
    }

    /**
     * Build Saving Throws from existing values.
     */
    public static function fromThrows(
        SavingThrow $strength,
        SavingThrow $dexterity,
        SavingThrow $constitution,
        SavingThrow $intelligence,
        SavingThrow $wisdom,
        SavingThrow $charisma
    ): self {
        return new self(
            $strength,
            $dexterity,
            $constitution,
            $intelligence,
            $wisdom,
            $charisma
        );
    }

    public function strength(): SavingThrow
    {
        return $this->strength;
    }

    public function dexterity(): SavingThrow
    {
        return $this->dexterity;
    }

    public function constitution(): SavingThrow
    {
        return $this->constitution;
    }

    public function intelligence(): SavingThrow
    {
        return $this->intelligence;
    }

    public function wisdom(): SavingThrow
    {
        return $this->wisdom;
    }

    public function charisma(): SavingThrow
    {
        return $this->charisma;
    }

    /**
     * Retrieve a Saving Throw by ability identifier.
     */
    public function get(
        string $ability
    ): SavingThrow {
        $ability = self::normaliseAbility(
            $ability
        );

        return match ($ability) {
            'strength' => $this->strength,
            'dexterity' => $this->dexterity,
            'constitution' => $this->constitution,
            'intelligence' => $this->intelligence,
            'wisdom' => $this->wisdom,
            'charisma' => $this->charisma,
        };
    }

    /**
     * Return all Saving Throws.
     *
     * @return array<string,SavingThrow>
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

    /**
     * Return proficient ability identifiers.
     *
     * @return string[]
     */
    public function proficiencies(): array
    {
        return array_keys(
            array_filter(
                $this->all(),
                static fn (
                    SavingThrow $savingThrow
                ): bool => $savingThrow->isProficient()
            )
        );
    }

    /**
     * Determine whether this collection equals another.
     */
    public function equals(
        self $other
    ): bool {
        foreach ($this->all() as $ability => $savingThrow) {
            if (
                ! $savingThrow->equals(
                    $other->get($ability)
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Normalise and validate proficiency identifiers.
     *
     * @param string[] $abilities
     *
     * @return string[]
     */
    private static function normaliseProficiencies(
        array $abilities
    ): array {
        $normalised = [];

        foreach ($abilities as $ability) {
            if (! is_string($ability)) {
                throw new InvalidArgumentException(
                    'Saving Throw proficiency identifiers must be strings.'
                );
            }

            $normalised[] = self::normaliseAbility(
                $ability
            );
        }

        return array_values(
            array_unique($normalised)
        );
    }

    /**
     * Normalise an ability identifier.
     */
    private static function normaliseAbility(
        string $ability
    ): string {
        $ability = strtolower(
            trim($ability)
        );

        if (
            ! in_array(
                $ability,
                self::ABILITIES,
                true
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'The saving-throw ability "%s" is not supported.',
                    $ability
                )
            );
        }

        return $ability;
    }
}
