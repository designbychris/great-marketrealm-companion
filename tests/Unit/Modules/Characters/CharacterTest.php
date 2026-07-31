<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Characters\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use PHPUnit\Framework\TestCase;

final class CharacterTest extends TestCase
{
    public function test_it_can_create_a_new_character(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium')
        );

        $this->assertInstanceOf(
            Character::class,
            $character
        );
    }

    public function test_it_returns_its_identifier(): void
    {
        $id = CharacterId::generate();

        $character = Character::create(
            $id,
            CharacterName::fromString('Sir Allium')
        );

        $this->assertTrue(
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
            $name
        );

        $this->assertTrue(
            $character
                ->name()
                ->equals($name)
        );
    }

    public function test_new_characters_start_at_level_one(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium')
        );

        $this->assertTrue(
            $character
                ->level()
                ->equals(Level::one())
        );
    }

    public function test_new_characters_start_with_zero_experience(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium')
        );

        $this->assertTrue(
            $character
                ->experience()
                ->equals(Experience::zero())
        );
    }

    public function test_it_can_be_renamed(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium')
        );

        $newName = CharacterName::fromString(
            'Lady Leek'
        );

        $character->rename($newName);

        $this->assertTrue(
            $character
                ->name()
                ->equals($newName)
        );
    }

    public function test_gaining_experience_updates_the_experience_total(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium')
        );

        $character->gainExperience(
            Experience::fromInt(150)
        );

        $this->assertTrue(
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
            CharacterName::fromString('Sir Allium')
        );

        $character->gainExperience(
            Experience::fromInt(300)
        );

        $this->assertTrue(
            $character
                ->level()
                ->equals(Level::fromInt(2))
        );
    }

    public function test_large_experience_gains_can_level_multiple_times(): void
    {
        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Sir Allium')
        );

        $character->gainExperience(
            Experience::fromInt(6500)
        );

        $this->assertTrue(
            $character
                ->level()
                ->equals(Level::fromInt(5))
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

        $character = Character::reconstitute(
            $id,
            $name,
            $level,
            $experience
        );

        $this->assertTrue(
            $character
                ->id()
                ->equals($id)
        );

        $this->assertTrue(
            $character
                ->name()
                ->equals($name)
        );

        $this->assertTrue(
            $character
                ->level()
                ->equals($level)
        );

        $this->assertTrue(
            $character
                ->experience()
                ->equals($experience)
        );
    }
}
