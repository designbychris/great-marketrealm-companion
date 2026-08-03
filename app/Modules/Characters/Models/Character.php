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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Initiative;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PassivePerception;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SavingThrows;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SkillProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Skills;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Speed;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Conditions;

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
        private Conditions $conditions,
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
            conditions: Conditions::none(),
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
        ?Conditions $conditions = null,
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
            conditions: $conditions
                ?? Conditions::none(),
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
     * Calculate the Character's initiative modifier.
     */
    public function initiative(): Initiative
    {
        return Initiative::fromDexterity(
            $this->abilityScores
                ->dexterity()
        );
    }

    /**
     * Calculate the Character's Passive Perception.
     */
    public function passivePerception(): PassivePerception
    {
        return PassivePerception::fromWisdom(
            $this->abilityScores
                ->wisdom()
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
     * Calculate the Character's saving throws.
     */
    public function savingThrows(): SavingThrows
    {
        return SavingThrows::fromAbilityScores(
            $this->abilityScores,
            $this->proficiencyBonus(),
            $this->characterClass
                ->savingThrowProficiencies()
        );
    }

    /**
     * Return the Character's skill proficiencies.
     *
     * Class, race, background, feat and equipment sources
     * will be merged here as those systems are introduced.
     */
    public function skillProficiencies(): SkillProficiencies
    {
        return SkillProficiencies::none();
    }

    /**
     * Calculate the Character's skill modifiers.
     */
    public function skills(): Skills
    {
        return Skills::fromAbilityScores(
            $this->abilityScores,
            $this->proficiencyBonus(),
            $this->skillProficiencies()
        );
    }

    /**
     * Calculate the Character's current walking speed.
     *
     * Race-based movement rules will replace the standard
     * speed once race definitions are connected to the domain.
     */
    public function speed(): Speed
    {
        return Speed::standard();
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
     * Return the Character's current conditions.
     */
    public function conditions(): Conditions
    {
        return $this->conditions;
    }
    
    /**
     * Apply a condition to the Character.
     */
    public function applyCondition(
        string $condition
    ): void {
        $this->conditions = $this->conditions->add(
            $condition
        );
    }
    
    /**
     * Remove a condition from the Character.
     */
    public function removeCondition(
        string $condition
    ): void {
        $this->conditions = $this->conditions->remove(
            $condition
        );
    }
    
    /**
     * Determine whether the Character has a condition.
     */
    public function hasCondition(
        string $condition
    ): bool {
        return $this->conditions->has(
            $condition
        );
    }

    /**
     * Determine whether the Character is conscious.
     */
    public function isConscious(): bool
    {
        return $this->hitPoints->isConscious();
    }
}
