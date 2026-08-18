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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Languages;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Spellbook;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterPurse;

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
        private Background $background,
        private Level $level,
        private Experience $experience,
        private HitPoints $hitPoints,
        private AbilityScores $abilityScores,
        private Conditions $conditions,
        private Languages $selectedLanguages,
        private ToolProficiencies $selectedToolProficiencies,
        private Spellbook $spellbook,
        private CallingPath $callingPath,
        private PathGifts $pathGifts,
        private CharacterPurse $purse,
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
        ?Background $background = null,
        ?Languages $selectedLanguages = null,
        ?ToolProficiencies $selectedToolProficiencies = null,
        ?Spellbook $spellbook = null,
        ?CallingPath $callingPath = null,
        ?PathGifts $pathGifts = null,
        ?CharacterPurse $purse = null,
    ): self {
        return new self(
            id: $id,
            name: $name,
            race: $race,
            characterClass: $characterClass,
            background: $background
                ?? Background::fromString(
                    'market-runner'
                ),
            level: Level::one(),
            experience: Experience::zero(),
            hitPoints: $hitPoints,
            abilityScores: $abilityScores,
            conditions: Conditions::none(),
            selectedLanguages:
                $selectedLanguages
                ?? Languages::none(),
            selectedToolProficiencies:
                $selectedToolProficiencies
                ?? ToolProficiencies::none(),
            spellbook: $spellbook
                ?? Spellbook::empty(),
            callingPath: $callingPath
                ?? CallingPath::none(),
            pathGifts: $pathGifts
                ?? PathGifts::none(),
            purse: $purse
                ?? CharacterPurse::empty(),
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
        ?Background $background = null,
        ?Languages $selectedLanguages = null,
        ?ToolProficiencies $selectedToolProficiencies = null,
        ?Spellbook $spellbook = null,
        ?CallingPath $callingPath = null,
        ?PathGifts $pathGifts = null,
        ?CharacterPurse $purse = null,
    ): self {
        return new self(
            id: $id,
            name: $name,
            race: $race,
            characterClass: $characterClass,
            background: $background
                ?? Background::fromString(
                    'market-runner'
                ),
            level: $level,
            experience: $experience,
            hitPoints: $hitPoints,
            abilityScores: $abilityScores,
            conditions: $conditions
                ?? Conditions::none(),
            selectedLanguages:
                $selectedLanguages
                ?? Languages::none(),
            selectedToolProficiencies:
                $selectedToolProficiencies
                ?? ToolProficiencies::none(),
            spellbook: $spellbook
                ?? Spellbook::empty(),
            callingPath: $callingPath
                ?? CallingPath::none(),
            pathGifts: $pathGifts
                ?? PathGifts::none(),
            purse: $purse
                ?? CharacterPurse::empty(),
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
     * Get the Character background.
     */
    public function background(): Background
    {
        return $this->background;
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
     * Get the adventurer's personal coin purse.
     */
    public function purse(): CharacterPurse
    {
        return $this->purse;
    }

    /**
     * Add personal coin to the adventurer's purse.
     */
    public function depositToPurse(
        CharacterPurse $amount
    ): void {
        $this->purse = $this->purse->deposit($amount);
    }

    /**
     * Remove personal coin from the adventurer's purse.
     */
    public function withdrawFromPurse(
        CharacterPurse $amount
    ): void {
        $this->purse = $this->purse->withdraw($amount);
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
     * Additional class, race, feat and equipment sources
     * can be merged here as those systems are introduced.
     */
    public function skillProficiencies(): SkillProficiencies
    {
        return $this->background
            ->skillProficiencies();
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
     * Return the Character's known fixed languages.
     *
     * Chosen languages will later be merged with this
     * background-provided collection.
     */
    public function languages(): Languages
    {
        return Languages::fromStrings(
            $this->background
                ->fixedLanguageIdentifiers()
        )->merge(
            $this->selectedLanguages
        );
    }

    /**
     * Return languages explicitly selected during registration.
     */
    public function selectedLanguages(): Languages
    {
        return $this->selectedLanguages;
    }
    
    /**
     * Return the Character's tool proficiencies.
     */
    public function toolProficiencies(): ToolProficiencies
    {
        $backgroundTools =
            ToolProficiencies::fromStrings(
                $this->background
                    ->toolProficiencyIdentifiers()
            );

        /*
         * Preserve legacy unresolved categories until a Character has
         * actually completed registration. Once explicit selections exist,
         * replace those category placeholders with the concrete choices.
         */
        if (
            $this->selectedToolProficiencies
                ->isEmpty()
        ) {
            return $backgroundTools;
        }

        foreach (
            $backgroundTools->unresolvedChoices()
            as $choice
        ) {
            $backgroundTools =
                $backgroundTools->remove(
                    $choice
                );
        }

        return $backgroundTools->merge(
            $this->selectedToolProficiencies
        );
    }

    /**
     * Return concrete tools explicitly selected during registration.
     */
    public function selectedToolProficiencies(): ToolProficiencies
    {
        return $this->selectedToolProficiencies;
    }

    /**
     * Return the Character's permanently certified spellbook.
     */
    public function spellbook(): Spellbook
    {
        return $this->spellbook;
    }

    /**
     * Return the permanently certified Path of Calling.
     */
    public function callingPath(): CallingPath
    {
        return $this->callingPath;
    }

    /**
     * Return the permanently certified Gifts of the Path.
     */
    public function pathGifts(): PathGifts
    {
        return $this->pathGifts;
    }

    /**
     * Permanently grant Path features during Guild Certification.
     *
     * @param array<int,string> $giftKeys
     */
    public function grantPathGifts(array $giftKeys): void
    {
        $this->pathGifts = $this->pathGifts->grant($giftKeys);
    }

    /**
     * Permanently enter a Path of Calling into the Guild Record.
     *
     * Paths are intentionally immutable once certified. Future respec rules,
     * if any, must be a separate explicit lifecycle.
     */
    public function chooseCallingPath(
        CallingPath $path
    ): void {
        if (! $path->isChosen()) {
            throw new \LogicException(
                'A certified Calling Path cannot be empty.'
            );
        }

        if (
            $this->callingPath->isChosen()
            && ! $this->callingPath->equals($path)
        ) {
            throw new \LogicException(
                'This adventurer already has a certified Calling Path.'
            );
        }

        $this->callingPath = $path;
    }

    /**
     * @param array<int,string> $spells
     * @param array<int,string> $cantrips
     */
    public function learnArcana(
        array $spells = [],
        array $cantrips = []
    ): void {
        $this->spellbook = $this->spellbook->learn(
            $spells,
            $cantrips
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
     * Change the Character background.
     */
    public function changeBackground(
        Background $background
    ): void {
        $this->background = $background;

        /*
         * Background-specific selections cannot safely survive a background
         * change unless the Registrar supplies fresh choices.
         */
        $this->selectedLanguages =
            Languages::none();

        $this->selectedToolProficiencies =
            ToolProficiencies::none();
    }

    /**
     * Complete or amend the background-dependent registration record.
     */
    public function completeRegistration(
        Background $background,
        Languages $languages,
        ToolProficiencies $tools
    ): void {
        $this->background = $background;
        $this->selectedLanguages = $languages;
        $this->selectedToolProficiencies = $tools;
    }

    /**
     * Award experience to the Character.
     *
     * Experience and Guild-certified level are deliberately separate.
     * Crossing an XP threshold makes the Character eligible for advancement;
     * the level itself is only changed by the Advancement workflow.
     */
    public function gainExperience(
        Experience $experience
    ): void {
        $this->experience = $this->experience->gain(
            $experience->value()
        );
    }

    /**
     * Determine whether earned experience permits another certification.
     */
    public function canAdvance(): bool
    {
        return $this->experience->canLevelUp(
            $this->level
        );
    }

    /**
     * Return the highest level unlocked by earned experience.
     */
    public function highestEligibleLevel(): Level
    {
        $earned = $this->experience->currentLevel();

        return $earned->value() < $this->level->value()
            ? $this->level
            : $earned;
    }

    /**
     * Return the number of level certifications currently waiting.
     */
    public function pendingAdvancementLevels(): int
    {
        return max(
            0,
            $this->highestEligibleLevel()->value()
                - $this->level->value()
        );
    }

    /**
     * Apply one Guild-certified level advancement.
     *
     * Experience eligibility is checked again here so the domain cannot be
     * advanced merely because a controller asked it to.
     */
    public function certifyAdvancement(
        int $hitPointGain
    ): void {
        if (! $this->canAdvance()) {
            throw new \LogicException(
                'The Character is not eligible for Guild advancement.'
            );
        }

        if ($this->level->isMaximum()) {
            throw new \LogicException(
                'A Character cannot advance beyond Level 20.'
            );
        }

        $this->hitPoints = $this
            ->hitPoints
            ->increaseMaximum($hitPointGain);

        $this->level = $this->level->next();
    }

    /**
     * Update mutable hit point values used during active play.
     *
     * The certified maximum remains untouched.
     */
    public function updateVitalMeasures(
        int $current,
        int $temporary
    ): void {
        $this->hitPoints = $this
            ->hitPoints
            ->withLiveState(
                $current,
                $temporary
            );
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
