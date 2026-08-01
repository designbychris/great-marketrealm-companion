<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Characters\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use PHPUnit\Framework\TestCase;

final class CharacterTest extends TestCase
{
    public function test_it_can_create_a_new_character(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

        self::assertInstanceOf(
            Character::class,
            $character
        );
    }

    public function test_it_returns_its_identifier(): void
    {
        $id = CharacterId::generate();

        $character = Character::create(
            $id,
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

        self::assertTrue(
            $character
                ->id()
                ->equals($id)
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
            HitPoints::full(12)
        );

        self::assertTrue(
            $character
                ->name()
                ->equals($name)
        );
    }

    public function test_it_returns_its_hit_points(): void
    {
        $hitPoints = HitPoints::full(12);

        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            $hitPoints
        );

        self::assertTrue(
            $character
                ->hitPoints()
                ->equals($hitPoints)
        );
    }

    public function test_new_characters_start_at_level_one(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

        self::assertTrue(
            $character
                ->level()
                ->equals(Level::one())
        );
    }

    public function test_new_characters_start_with_zero_experience(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

        self::assertTrue(
            $character
                ->experience()
                ->equals(Experience::zero())
        );
    }

    public function test_it_can_be_renamed(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

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
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

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
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

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
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

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
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

        $character->takeDamage(5);

        self::assertSame(
            7,
            $character
                ->hitPoints()
                ->current()
        );
    }

    public function test_it_can_be_healed(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::fromValues(
                current: 6,
                maximum: 12
            )
        );

        $character->heal(4);

        self::assertSame(
            10,
            $character
                ->hitPoints()
                ->current()
        );
    }

    public function test_it_can_receive_temporary_hit_points(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::full(12)
        );

        $character->grantTemporaryHitPoints(4);

        self::assertSame(
            4,
            $character
                ->hitPoints()
                ->temporary()
        );
    }

    public function test_temporary_hit_points_absorb_damage_first(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::fromValues(
                current: 10,
                maximum: 12,
                temporary: 4
            )
        );

        $character->takeDamage(3);

        self::assertSame(
            10,
            $character
                ->hitPoints()
                ->current()
        );

        self::assertSame(
            1,
            $character
                ->hitPoints()
                ->temporary()
        );
    }

    public function test_it_is_conscious_while_above_zero_hit_points(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium'),
            HitPoints::fromValues(
                current: 1,
                maximum: 12
            )
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
            HitPoints::fromValues(
                current: 0,
                maximum: 12
            )
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

        $level = Level::fromInt(7);

        $experience = Experience::fromInt(
            26000
        );

        $hitPoints = HitPoints::fromValues(
            current: 34,
            maximum: 42,
            temporary: 5
        );

        $character = Character::reconstitute(
            $id,
            $name,
            $level,
            $experience,
            $hitPoints
        );

        self::assertTrue(
            $character
                ->id()
                ->equals($id)
        );

        self::assertTrue(
            $character
                ->name()
                ->equals($name)
        );

        self::assertTrue(
            $character
                ->level()
                ->equals($level)
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
    }
}
