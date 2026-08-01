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

defined('ABSPATH') || exit;

/**
 * Character Factory.
 *
 * Creates fully initialised Character entities from
 * validated character-creation input.
 *
 * The factory owns creation defaults and calculations
 * that do not belong in the HTTP controller.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CharacterFactory
{
    /**
     * Create a new Character.
     */
    public function create(
        CharacterName $name,
        Race $race,
        CharacterClass $characterClass,
        AbilityScores $abilityScores
    ): Character {
        $startingHitPoints = $characterClass
            ->startingHitPoints(
                $abilityScores->constitution()
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
                ?? AbilityScores::average()
        );
    }
}
