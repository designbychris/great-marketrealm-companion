<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Rules\CharacterCreationRules;

defined('ABSPATH') || exit;

/**
 * Character Factory.
 *
 * Creates fully initialised Character entities from
 * validated character-creation input.
 *
 * The factory coordinates Character creation while
 * CharacterCreationRules owns creation defaults and
 * gameplay calculations.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CharacterFactory
{
    /**
     * Create the Character factory.
     */
    public function __construct(
        private CharacterCreationRules $rules
    ) {
    }

    /**
     * Create a new Character from domain values.
     */
    public function create(
        CharacterName $name,
        Race $race,
        CharacterClass $characterClass,
        AbilityScores $abilityScores
    ): Character {
        $startingHitPoints = $this->rules
            ->startingHitPoints(
                $characterClass,
                $abilityScores
            );

        return Character::create(
            CharacterId::generate(),
            $name,
            $race,
            $characterClass,
            HitPoints::full(
                $startingHitPoints
            ),
            $abilityScores
        );
    }

    /**
     * Create a new Character from primitive input.
     *
     * This is useful at application boundaries such as
     * HTTP requests, imports and command-line tools.
     */
    public function fromInput(
        string $name,
        string $race,
        string $characterClass,
        ?AbilityScores $abilityScores = null
    ): Character {
        return $this->create(
            CharacterName::fromString($name),
            Race::fromString($race),
            CharacterClass::fromString(
                $characterClass
            ),
            $abilityScores
                ?? $this->rules->defaultAbilityScores()
        );
    }
}
