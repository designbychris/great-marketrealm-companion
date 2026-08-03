<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ArmourClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;

defined('ABSPATH') || exit;

/**
 * Character Entity.
 *
 * Represents a playable character within the
 * Great Marketrealm.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class Character
{
    /**
     * Character constructor.
     */
    private function __construct(
        private CharacterId $id,
        private CharacterName $name,
        private Race $race,
        private CharacterClass $characterClass,
        private Level $level,
        private Experience $experience,
        private HitPoints $hitPoints,
        private AbilityScores $abilityScores,
    ) {
    }

    /**
     * Create a brand-new Character.
     */
    public static function create(
        CharacterId $id,
        CharacterName $name,
        Race $race,
        CharacterClass $characterClass,
        HitPoints $hitPoints,
        AbilityScores $abilityScores,
    ): self {
        return new self(
            id: $id,
            name: $name,
            race: $race,
            characterClass: $characterClass,
            level: Level::one(),
            experience: Experience::zero(),
            hitPoints: $hitPoints,
            abilityScores: $abilityScores,
        );
    }

    /**
     * Reconstitute an existing Character.
     *
     * Used by repositories when rebuilding an entity
     * from persistence.
     */
    public static function reconstitute(
        CharacterId $id,
        CharacterName $name,
        Race $race,
        CharacterClass $characterClass,
        Level $level,
        Experience $experience,
        HitPoints $hitPoints,
        AbilityScores $abilityScores,
    ): self {
        return new self(
            id: $id,
            name: $name,
            race: $race,
            characterClass: $characterClass,
            level: $level,
            experience: $experience,
            hitPoints: $hitPoints,
            abilityScores: $abilityScores,
        );
    }

    /**
     * Get the Character identifier.
     */
    public function id(): CharacterId
    {
        return $this->id;
    }

    /**
     * Get the Character name.
     */
    public function name(): CharacterName
    {
        return $this->name;
    }

    /**
     * Get the Character race.
     */
    public function race(): Race
    {
        return $this->race;
    }

    /**
     * Get the Character class.
     */
    public function characterClass(): CharacterClass
    {
        return $this->characterClass;
    }

    /**
     * Get the Character level.
     */
    public function level(): Level
    {
        return $this->level;
    }

    /**
     * Get the Character experience.
     */
    public function experience(): Experience
    {
        return $this->experience;
    }

    /**
     * Get the Character hit points.
     */
    public function hitPoints(): HitPoints
    {
        return $this->hitPoints;
    }

    /**
     * Get the Character ability scores.
     */
    public function abilityScores(): AbilityScores
    {
        return $this->abilityScores;
    }

    /**
     * Calculate the Character's current Armour Class.
     *
     * Until equipment and armour are implemented, Armour Class
     * is derived from the Character's Dexterity modifier.
     */
    public function armourClass(): ArmourClass
    {
        return ArmourClass::unarmoured(
            $this->abilityScores
                ->dexterity()
        );
    }

    /**
     * Calculate the Character's proficiency bonus.
     */
    public function proficiencyBonus(): ProficiencyBonus
    {
        return ProficiencyBonus::fromLevel(
            $this->level
        );
    }

    /**
     * Rename the Character.
     */
    public function rename(
        CharacterName $name
    ): void {
        $this->name = $name;
    }

    /**
     * Award experience to the Character.
     */
    public function gainExperience(
        Experience $experience
    ): void {
        $this->experience = $this->experience->gain(
            $experience->value()
        );

        while (
            $this->experience->canLevelUp(
                $this->level
            )
        ) {
            $this->level = $this->level->next();
        }
    }

    /**
     * Apply damage to the Character.
     */
    public function takeDamage(int $amount): void
    {
        $this->hitPoints = $this->hitPoints->takeDamage(
            $amount
        );
    }

    /**
     * Restore hit points to the Character.
     */
    public function heal(int $amount): void
    {
        $this->hitPoints = $this->hitPoints->heal(
            $amount
        );
    }

    /**
     * Grant temporary hit points to the Character.
     */
    public function grantTemporaryHitPoints(
        int $amount
    ): void {
        $this->hitPoints = $this
            ->hitPoints
            ->grantTemporary($amount);
    }

    /**
     * Determine whether the Character is conscious.
     */
    public function isConscious(): bool
    {
        return $this->hitPoints->isConscious();
    }
}
