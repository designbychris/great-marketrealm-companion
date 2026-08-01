<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Services\CharacterFactory;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CharacterFactoryTest extends TestCase
{
    public function testCreatesACharacterFromDomainValues(): void
    {
        $factory = new CharacterFactory();

        $character = $factory->create(
            \GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName::fromString(
                'Sir Allium'
            ),
            \GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race::fromString(
                'fructan'
            ),
            \GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass::fromString(
                'fighter'
            ),
            AbilityScores::average()
        );

        self::assertInstanceOf(
            Character::class,
            $character
        );
    }

    public function testCreatesACharacterFromPrimitiveInput(): void
    {
        $character = $this->factory()->fromInput(
            name: 'Sir Allium',
            race: 'fructan',
            characterClass: 'fighter'
        );

        self::assertSame(
            'Sir Allium',
            $character->name()->value()
        );

        self::assertSame(
            'fructan',
            $character->race()->value()
        );

        self::assertSame(
            'fighter',
            $character->characterClass()->value()
        );
    }

    public function testGeneratesACharacterIdentifier(): void
    {
        $character = $this->createCharacter();

        self::assertNotSame(
            '',
            $character->id()->value()
        );
    }

    public function testGeneratesDifferentIdentifiersForDifferentCharacters(): void
    {
        $first = $this->createCharacter();

        $second = $this->factory()->fromInput(
            name: 'Lady Leek',
            race: 'vegfolk',
            characterClass: 'wizard'
        );

        self::assertFalse(
            $first->id()->equals(
                $second->id()
            )
        );
    }

    public function testNewCharactersBeginAtLevelOne(): void
    {
        self::assertTrue(
            $this->createCharacter()
                ->level()
                ->equals(Level::one())
        );
    }

    public function testNewCharactersBeginWithZeroExperience(): void
    {
        self::assertTrue(
            $this->createCharacter()
                ->experience()
                ->equals(Experience::zero())
        );
    }

    public function testUsesAverageAbilityScoresByDefault(): void
    {
        self::assertTrue(
            $this->createCharacter()
                ->abilityScores()
                ->equals(
                    AbilityScores::average()
                )
        );
    }

    public function testUsesSuppliedAbilityScores(): void
    {
        $abilityScores = $this->customAbilityScores();

        $character = $this->factory()->fromInput(
            name: 'Sir Allium',
            race: 'fructan',
            characterClass: 'fighter',
            abilityScores: $abilityScores
        );

        self::assertTrue(
            $character
                ->abilityScores()
                ->equals($abilityScores)
        );
    }

    public function testCalculatesFighterStartingHitPoints(): void
    {
        $character = $this->factory()->fromInput(
            name: 'Sir Allium',
            race: 'fructan',
            characterClass: 'fighter',
            abilityScores: $this->customAbilityScores()
        );

        /*
         * Fighter hit die: 10
         * Constitution 14: +2
         */
        self::assertSame(
            12,
            $character->hitPoints()->maximum()
        );

        self::assertSame(
            12,
            $character->hitPoints()->current()
        );
    }

    public function testCalculatesBarbarianStartingHitPoints(): void
    {
        $character = $this->factory()->fromInput(
            name: 'Ribald',
            race: 'meatfolk',
            characterClass: 'barbarian',
            abilityScores: AbilityScores::fromScores(
                strength: AbilityScore::fromInt(16),
                dexterity: AbilityScore::fromInt(12),
                constitution: AbilityScore::fromInt(16),
                intelligence: AbilityScore::fromInt(8),
                wisdom: AbilityScore::fromInt(10),
                charisma: AbilityScore::fromInt(10),
            )
        );

        /*
         * Barbarian hit die: 12
         * Constitution 16: +3
         */
        self::assertSame(
            15,
            $character->hitPoints()->maximum()
        );
    }

    public function testStartingTemporaryHitPointsAreZero(): void
    {
        self::assertSame(
            0,
            $this->createCharacter()
                ->hitPoints()
                ->temporary()
        );
    }

    public function testNormalisesPrimitiveInput(): void
    {
        $character = $this->factory()->fromInput(
            name: '  Sir Allium  ',
            race: '  FRUCTAN  ',
            characterClass: '  FIGHTER  '
        );

        self::assertSame(
            'Sir Allium',
            $character->name()->value()
        );

        self::assertSame(
            'fructan',
            $character->race()->value()
        );

        self::assertSame(
            'fighter',
            $character->characterClass()->value()
        );
    }

    public function testRejectsAnUnsupportedRace(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->factory()->fromInput(
            name: 'Sir Allium',
            race: 'sandwich-person',
            characterClass: 'fighter'
        );
    }

    public function testRejectsAnUnsupportedCharacterClass(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->factory()->fromInput(
            name: 'Sir Allium',
            race: 'fructan',
            characterClass: 'sandwich-knight'
        );
    }

    public function testRejectsAnInvalidCharacterName(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->factory()->fromInput(
            name: '',
            race: 'fructan',
            characterClass: 'fighter'
        );
    }

    private function factory(): CharacterFactory
    {
        return new CharacterFactory();
    }

    private function createCharacter(): Character
    {
        return $this->factory()->fromInput(
            name: 'Sir Allium',
            race: 'fructan',
            characterClass: 'fighter'
        );
    }

    private function customAbilityScores(): AbilityScores
    {
        return AbilityScores::fromScores(
            strength: AbilityScore::fromInt(15),
            dexterity: AbilityScore::fromInt(14),
            constitution: AbilityScore::fromInt(14),
            intelligence: AbilityScore::fromInt(12),
            wisdom: AbilityScore::fromInt(10),
            charisma: AbilityScore::fromInt(8),
        );
    }
}
