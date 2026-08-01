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
}
