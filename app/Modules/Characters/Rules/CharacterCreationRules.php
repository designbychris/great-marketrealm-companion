<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Rules;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;

defined('ABSPATH') || exit;

/**
 * Character Creation Rules.
 *
 * Defines the default progression, abilities and hit-point
 * rules applied when a new Character enters the Marketrealm.
 *
 * This class contains creation rules only. It does not create
 * Character entities, generate identifiers or persist data.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CharacterCreationRules
{
    /**
     * Return the default ability scores for a new Character.
     */
    public function defaultAbilityScores(): AbilityScores
    {
        return AbilityScores::average();
    }

    /**
     * Calculate the starting maximum hit points.
     */
    public function startingHitPoints(
        CharacterClass $characterClass,
        AbilityScores $abilityScores
    ): int {
        return $characterClass->startingHitPoints(
            $abilityScores->constitution()
        );
    }

    /**
     * Return the starting Character level.
     */
    public function startingLevel(): Level
    {
        return Level::one();
    }

    /**
     * Return the starting Character experience.
     */
    public function startingExperience(): Experience
    {
        return Experience::zero();
    }
}
