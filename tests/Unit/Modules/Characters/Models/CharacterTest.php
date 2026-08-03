<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Characters\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ArmourClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Speed;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Initiative;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PassivePerception;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SavingThrow;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SavingThrows;
use PHPUnit\Framework\TestCase;

final class CharacterTest extends TestCase
{
    public function test_it_can_create_a_new_character(): void
    {
        self::assertInstanceOf(
            Character::class,
            $this->createCharacter()
        );
    }

    public function test_it_returns_its_identifier(): void
    {
        $id = CharacterId::generate();

        $character = Character::create(
            $id,
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(10),
            AbilityScores::average()
        );

        self::assertTrue(
            $character->id()->equals($id)
        );
    }

    public function test_it_returns_its_name(): void
    {
        $name = CharacterName::fromString(
            'Sir Allium'
        );

        $character = Character::create(
            CharacterId::generate(),
            $name,
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(10),
            AbilityScores::average()
        );

        self::assertTrue(
            $character->name()->equals($name)
        );
    }

    public function test_it_returns_its_race(): void
    {
        $race = Race::fromString(
            'fructan'
        );

        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            $race,
            CharacterClass::fromString('fighter'),
            HitPoints::full(10),
            AbilityScores::average()
        );

        self::assertTrue(
            $character->race()->equals($race)
        );
    }

    public function test_it_returns_its_character_class(): void
    {
        $class = CharacterClass::fromString(
            'fighter'
        );

        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            $class,
            HitPoints::full(10),
            AbilityScores::average()
        );

        self::assertTrue(
            $character
                ->characterClass()
                ->equals($class)
        );
    }

    public function test_it_returns_its_hit_points(): void
    {
        $hitPoints = HitPoints::full(12);

        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            $hitPoints,
            AbilityScores::average()
        );

        self::assertTrue(
            $character
                ->hitPoints()
                ->equals($hitPoints)
        );
    }

    public function test_it_returns_its_ability_scores(): void
    {
        $abilityScores = $this->abilityScores();

        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            $abilityScores
        );

        self::assertTrue(
            $character
                ->abilityScores()
                ->equals($abilityScores)
        );
    }

    public function test_it_returns_an_armour_class(): void
    {
        self::assertInstanceOf(
            ArmourClass::class,
            $this->createCharacter()
                ->armourClass()
        );
    }
    
    public function test_average_dexterity_gives_armour_class_ten(): void
    {
        self::assertSame(
            10,
            $this->createCharacter()
                ->armourClass()
                ->value()
        );
    }
    
    public function test_dexterity_fourteen_gives_armour_class_twelve(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            AbilityScores::fromScores(
                strength: AbilityScore::fromInt(15),
                dexterity: AbilityScore::fromInt(14),
                constitution: AbilityScore::fromInt(13),
                intelligence: AbilityScore::fromInt(12),
                wisdom: AbilityScore::fromInt(10),
                charisma: AbilityScore::fromInt(8),
            )
        );
    
        self::assertSame(
            12,
            $character
                ->armourClass()
                ->value()
        );
    }
    
    public function test_dexterity_twenty_gives_armour_class_fifteen(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            AbilityScores::fromScores(
                strength: AbilityScore::fromInt(15),
                dexterity: AbilityScore::fromInt(20),
                constitution: AbilityScore::fromInt(13),
                intelligence: AbilityScore::fromInt(12),
                wisdom: AbilityScore::fromInt(10),
                charisma: AbilityScore::fromInt(8),
            )
        );
    
        self::assertSame(
            15,
            $character
                ->armourClass()
                ->value()
        );
    }
    
    public function test_armour_class_is_derived_from_dexterity(): void
    {
        $averageDexterityCharacter =
            $this->createCharacter();
    
        $higherDexterityCharacter =
            Character::create(
                CharacterId::generate(),
                CharacterName::fromString('Lady Leek'),
                Race::fromString('vegfolk'),
                CharacterClass::fromString('rogue'),
                HitPoints::full(10),
                AbilityScores::fromScores(
                    strength: AbilityScore::fromInt(10),
                    dexterity: AbilityScore::fromInt(16),
                    constitution: AbilityScore::fromInt(10),
                    intelligence: AbilityScore::fromInt(10),
                    wisdom: AbilityScore::fromInt(10),
                    charisma: AbilityScore::fromInt(10),
                )
            );
    
        self::assertSame(
            10,
            $averageDexterityCharacter
                ->armourClass()
                ->value()
        );
    
        self::assertSame(
            13,
            $higherDexterityCharacter
                ->armourClass()
                ->value()
        );
    }
    
    public function test_reconstituted_character_derives_armour_class_from_restored_ability_scores(): void
    {
        $abilityScores = $this->abilityScores();
    
        $character = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            Level::fromInt(7),
            Experience::fromInt(26000),
            HitPoints::fromValues(
                current: 34,
                maximum: 42,
                temporary: 5
            ),
            $abilityScores
        );
    
        self::assertTrue(
            $character
                ->armourClass()
                ->equals(
                    ArmourClass::unarmoured(
                        $abilityScores->dexterity()
                    )
                )
        );
    }

    public function test_new_characters_start_at_level_one(): void
    {
        self::assertTrue(
            $this->createCharacter()
                ->level()
                ->equals(Level::one())
        );
    }

    public function test_new_characters_start_with_zero_experience(): void
    {
        self::assertTrue(
            $this->createCharacter()
                ->experience()
                ->equals(Experience::zero())
        );
    }

    public function test_it_can_be_renamed(): void
    {
        $character = $this->createCharacter();

        $newName = CharacterName::fromString(
            'Lady Leek'
        );

        $character->rename($newName);

        self::assertTrue(
            $character
                ->name()
                ->equals($newName)
        );
    }

    public function test_gaining_experience_updates_the_experience_total(): void
    {
        $character = $this->createCharacter();

        $character->gainExperience(
            Experience::fromInt(150)
        );

        self::assertTrue(
            $character
                ->experience()
                ->equals(
                    Experience::fromInt(150)
                )
        );
    }

    public function test_gaining_enough_experience_levels_the_character_up(): void
    {
        $character = $this->createCharacter();

        $character->gainExperience(
            Experience::fromInt(300)
        );

        self::assertTrue(
            $character
                ->level()
                ->equals(Level::fromInt(2))
        );
    }

    public function test_large_experience_gains_can_level_multiple_times(): void
    {
        $character = $this->createCharacter();

        $character->gainExperience(
            Experience::fromInt(6500)
        );

        self::assertTrue(
            $character
                ->level()
                ->equals(Level::fromInt(5))
        );
    }

    public function test_it_can_take_damage(): void
    {
        $character = $this->createCharacter();

        $character->takeDamage(5);

        self::assertSame(
            5,
            $character->hitPoints()->current()
        );
    }

    public function test_it_can_be_healed(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::fromValues(
                current: 6,
                maximum: 12
            ),
            AbilityScores::average()
        );

        $character->heal(4);

        self::assertSame(
            10,
            $character->hitPoints()->current()
        );
    }

    public function test_it_can_receive_temporary_hit_points(): void
    {
        $character = $this->createCharacter();

        $character->grantTemporaryHitPoints(4);

        self::assertSame(
            4,
            $character->hitPoints()->temporary()
        );
    }

    public function test_temporary_hit_points_absorb_damage_first(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::fromValues(
                current: 10,
                maximum: 12,
                temporary: 4
            ),
            AbilityScores::average()
        );

        $character->takeDamage(3);

        self::assertSame(
            10,
            $character->hitPoints()->current()
        );

        self::assertSame(
            1,
            $character->hitPoints()->temporary()
        );
    }

    public function test_it_is_conscious_while_above_zero_hit_points(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::fromValues(
                current: 1,
                maximum: 12
            ),
            AbilityScores::average()
        );

        self::assertTrue(
            $character->isConscious()
        );
    }

    public function test_it_is_not_conscious_at_zero_hit_points(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::fromValues(
                current: 0,
                maximum: 12
            ),
            AbilityScores::average()
        );

        self::assertFalse(
            $character->isConscious()
        );
    }

    public function test_reconstituting_a_character_restores_its_state(): void
    {
        $id = CharacterId::generate();

        $name = CharacterName::fromString(
            'Sir Allium'
        );

        $race = Race::fromString(
            'fructan'
        );

        $class = CharacterClass::fromString(
            'fighter'
        );

        $level = Level::fromInt(7);

        $experience = Experience::fromInt(
            26000
        );

        $hitPoints = HitPoints::fromValues(
            current: 34,
            maximum: 42,
            temporary: 5
        );

        $abilityScores = $this->abilityScores();

        $character = Character::reconstitute(
            $id,
            $name,
            $race,
            $class,
            $level,
            $experience,
            $hitPoints,
            $abilityScores
        );

        self::assertTrue(
            $character->id()->equals($id)
        );

        self::assertTrue(
            $character->name()->equals($name)
        );

        self::assertTrue(
            $character->race()->equals($race)
        );

        self::assertTrue(
            $character
                ->characterClass()
                ->equals($class)
        );

        self::assertTrue(
            $character->level()->equals($level)
        );

        self::assertTrue(
            $character
                ->experience()
                ->equals($experience)
        );

        self::assertTrue(
            $character
                ->hitPoints()
                ->equals($hitPoints)
        );

        self::assertTrue(
            $character
                ->abilityScores()
                ->equals($abilityScores)
        );
    }

    private function createCharacter(): Character
    {
        return Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(10),
            AbilityScores::average()
        );
    }

    private function abilityScores(): AbilityScores
    {
        return AbilityScores::fromScores(
            strength: AbilityScore::fromInt(15),
            dexterity: AbilityScore::fromInt(14),
            constitution: AbilityScore::fromInt(13),
            intelligence: AbilityScore::fromInt(12),
            wisdom: AbilityScore::fromInt(10),
            charisma: AbilityScore::fromInt(8),
        );
    }

    public function test_it_returns_a_proficiency_bonus(): void
    {
        self::assertInstanceOf(
            ProficiencyBonus::class,
            $this->createCharacter()
                ->proficiencyBonus()
        );
    }
    
    public function test_level_one_character_has_proficiency_bonus_two(): void
    {
        self::assertSame(
            2,
            $this->createCharacter()
                ->proficiencyBonus()
                ->value()
        );
    }
    
    public function test_level_five_character_has_proficiency_bonus_three(): void
    {
        $character = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            Level::fromInt(5),
            Experience::fromInt(6500),
            HitPoints::full(20),
            AbilityScores::average()
        );
    
        self::assertSame(
            3,
            $character
                ->proficiencyBonus()
                ->value()
        );
    }
    
    public function test_level_seventeen_character_has_proficiency_bonus_six(): void
    {
        $character = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            Level::fromInt(17),
            Experience::fromInt(225000),
            HitPoints::full(100),
            AbilityScores::average()
        );
    
        self::assertSame(
            6,
            $character
                ->proficiencyBonus()
                ->value()
        );
    }
    
    public function test_proficiency_bonus_updates_when_experience_increases_level(): void
    {
        $character = $this->createCharacter();
    
        self::assertSame(
            2,
            $character
                ->proficiencyBonus()
                ->value()
        );
    
        $character->gainExperience(
            Experience::fromInt(6500)
        );
    
        self::assertSame(
            3,
            $character
                ->proficiencyBonus()
                ->value()
        );
    }
    
    public function test_reconstituted_character_derives_proficiency_bonus_from_restored_level(): void
    {
        $character = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            Level::fromInt(13),
            Experience::fromInt(120000),
            HitPoints::full(80),
            $this->abilityScores()
        );
    
        self::assertTrue(
            $character
                ->proficiencyBonus()
                ->equals(
                    ProficiencyBonus::fromInt(5)
                )
        );
    }

    public function test_it_returns_a_speed(): void
    {
        self::assertInstanceOf(
            Speed::class,
            $this->createCharacter()->speed()
        );
    }
    
    public function test_character_has_standard_speed(): void
    {
        self::assertSame(
            30,
            $this->createCharacter()
                ->speed()
                ->feet()
        );
    }
    
    public function test_character_speed_is_formatted_for_display(): void
    {
        self::assertSame(
            '30 ft',
            $this->createCharacter()
                ->speed()
                ->formatted()
        );
    }
    
    public function test_reconstituted_character_has_standard_speed(): void
    {
        $character = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            Level::fromInt(7),
            Experience::fromInt(26000),
            HitPoints::full(42),
            $this->abilityScores()
        );
    
        self::assertTrue(
            $character
                ->speed()
                ->equals(
                    Speed::standard()
                )
        );
    }


    public function test_it_returns_an_initiative(): void
    {
        self::assertInstanceOf(
            Initiative::class,
            $this->createCharacter()
                ->initiative()
        );
    }
    
    public function test_average_dexterity_gives_zero_initiative(): void
    {
        self::assertSame(
            0,
            $this->createCharacter()
                ->initiative()
                ->modifier()
        );
    }
    
    public function test_dexterity_fourteen_gives_plus_two_initiative(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            $this->abilityScores()
        );
    
        self::assertSame(
            2,
            $character
                ->initiative()
                ->modifier()
        );
    
        self::assertSame(
            '+2',
            $character
                ->initiative()
                ->signed()
        );
    }
    
    public function test_low_dexterity_gives_negative_initiative(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Slow Turnip'),
            Race::fromString('rootkin'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            AbilityScores::fromScores(
                strength: AbilityScore::fromInt(14),
                dexterity: AbilityScore::fromInt(8),
                constitution: AbilityScore::fromInt(14),
                intelligence: AbilityScore::fromInt(10),
                wisdom: AbilityScore::fromInt(10),
                charisma: AbilityScore::fromInt(10),
            )
        );
    
        self::assertSame(
            -1,
            $character
                ->initiative()
                ->modifier()
        );
    
        self::assertSame(
            '-1',
            $character
                ->initiative()
                ->signed()
        );
    }
    
    public function test_reconstituted_character_derives_initiative_from_restored_dexterity(): void
    {
        $abilityScores = $this->abilityScores();
    
        $character = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            Level::fromInt(7),
            Experience::fromInt(26000),
            HitPoints::full(42),
            $abilityScores
        );
    
        self::assertTrue(
            $character
                ->initiative()
                ->equals(
                    Initiative::fromDexterity(
                        $abilityScores->dexterity()
                    )
                )
        );
    }

    public function test_it_returns_passive_perception(): void
    {
        self::assertInstanceOf(
            PassivePerception::class,
            $this->createCharacter()
                ->passivePerception()
        );
    }
    
    public function test_average_wisdom_gives_passive_perception_ten(): void
    {
        self::assertSame(
            10,
            $this->createCharacter()
                ->passivePerception()
                ->value()
        );
    }
    
    public function test_wisdom_fourteen_gives_passive_perception_twelve(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            AbilityScores::fromScores(
                strength: AbilityScore::fromInt(15),
                dexterity: AbilityScore::fromInt(14),
                constitution: AbilityScore::fromInt(13),
                intelligence: AbilityScore::fromInt(12),
                wisdom: AbilityScore::fromInt(14),
                charisma: AbilityScore::fromInt(8),
            )
        );
    
        self::assertSame(
            12,
            $character
                ->passivePerception()
                ->value()
        );
    }
    
    public function test_low_wisdom_reduces_passive_perception(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Distracted Turnip'),
            Race::fromString('rootkin'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            AbilityScores::fromScores(
                strength: AbilityScore::fromInt(14),
                dexterity: AbilityScore::fromInt(10),
                constitution: AbilityScore::fromInt(14),
                intelligence: AbilityScore::fromInt(10),
                wisdom: AbilityScore::fromInt(8),
                charisma: AbilityScore::fromInt(10),
            )
        );
    
        self::assertSame(
            9,
            $character
                ->passivePerception()
                ->value()
        );
    }
    
    public function test_reconstituted_character_derives_passive_perception_from_restored_wisdom(): void
    {
        $abilityScores = $this->abilityScores();
    
        $character = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            Level::fromInt(7),
            Experience::fromInt(26000),
            HitPoints::full(42),
            $abilityScores
        );
    
        self::assertTrue(
            $character
                ->passivePerception()
                ->equals(
                    PassivePerception::fromWisdom(
                        $abilityScores->wisdom()
                    )
                )
        );
    }

    public function test_it_returns_saving_throws(): void
    {
        self::assertInstanceOf(
            SavingThrows::class,
            $this->createCharacter()
                ->savingThrows()
        );
    }

    public function test_fighter_has_strength_and_constitution_save_proficiencies(): void
    {
        $savingThrows = $this->createCharacter()
            ->savingThrows();
    
        self::assertSame(
            [
                'strength',
                'constitution',
            ],
            $savingThrows->proficiencies()
        );
    
        self::assertTrue(
            $savingThrows
                ->strength()
                ->isProficient()
        );
    
        self::assertTrue(
            $savingThrows
                ->constitution()
                ->isProficient()
        );
    }

    public function test_fighter_saving_throws_include_proficiency_bonus(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(12),
            AbilityScores::fromScores(
                strength: AbilityScore::fromInt(15),
                dexterity: AbilityScore::fromInt(14),
                constitution: AbilityScore::fromInt(13),
                intelligence: AbilityScore::fromInt(12),
                wisdom: AbilityScore::fromInt(10),
                charisma: AbilityScore::fromInt(8),
            )
        );
    
        $savingThrows = $character->savingThrows();
    
        /*
         * Strength 15: +2
         * Fighter proficiency at level 1: +2
         */
        self::assertSame(
            4,
            $savingThrows
                ->strength()
                ->modifier()
        );
    
        /*
         * Constitution 13: +1
         * Fighter proficiency at level 1: +2
         */
        self::assertSame(
            3,
            $savingThrows
                ->constitution()
                ->modifier()
        );
    }

    public function test_non_proficient_saving_throws_use_only_ability_modifier(): void
    {
        $savingThrows = $this->createCharacter()
            ->savingThrows();
    
        self::assertFalse(
            $savingThrows
                ->dexterity()
                ->isProficient()
        );
    
        self::assertSame(
            0,
            $savingThrows
                ->dexterity()
                ->modifier()
        );
    }

    public function test_paladin_has_wisdom_and_charisma_save_proficiencies(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Mr Meat'),
            Race::fromString('meatkin'),
            CharacterClass::fromString('paladin'),
            HitPoints::full(10),
            AbilityScores::average()
        );
    
        self::assertSame(
            [
                'wisdom',
                'charisma',
            ],
            $character
                ->savingThrows()
                ->proficiencies()
        );
    }

    public function test_saving_throw_modifiers_increase_when_proficiency_bonus_increases(): void
    {
        $character = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            Level::fromInt(5),
            Experience::fromInt(6500),
            HitPoints::full(30),
            AbilityScores::fromScores(
                strength: AbilityScore::fromInt(16),
                dexterity: AbilityScore::fromInt(12),
                constitution: AbilityScore::fromInt(14),
                intelligence: AbilityScore::fromInt(10),
                wisdom: AbilityScore::fromInt(10),
                charisma: AbilityScore::fromInt(10),
            )
        );
    
        $savingThrows = $character->savingThrows();
    
        /*
         * Strength 16: +3
         * Level 5 proficiency bonus: +3
         */
        self::assertSame(
            6,
            $savingThrows
                ->strength()
                ->modifier()
        );
    
        /*
         * Dexterity 12: +1
         * Not proficient
         */
        self::assertSame(
            1,
            $savingThrows
                ->dexterity()
                ->modifier()
        );
    }

    public function test_reconstituted_character_derives_saving_throws_from_restored_state(): void
    {
        $abilityScores = $this->abilityScores();
    
        $character = Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            Level::fromInt(7),
            Experience::fromInt(26000),
            HitPoints::full(42),
            $abilityScores
        );
    
        $expected = SavingThrows::fromAbilityScores(
            $abilityScores,
            ProficiencyBonus::fromLevel(
                Level::fromInt(7)
            ),
            [
                'strength',
                'constitution',
            ]
        );
    
        self::assertTrue(
            $character
                ->savingThrows()
                ->equals($expected)
        );
    }

    public function test_every_character_saving_throw_is_a_saving_throw_value_object(): void
    {
        self::assertContainsOnlyInstancesOf(
            SavingThrow::class,
            $this->createCharacter()
                ->savingThrows()
                ->all()
        );
    }
}
