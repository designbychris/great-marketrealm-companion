<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Rules;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Rules\CharacterCreationRules;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CharacterCreationRulesTest extends TestCase
{
    public function testReturnsAverageAbilityScoresByDefault(): void
    {
        $abilityScores = $this->rules()
            ->defaultAbilityScores();

        self::assertTrue(
            $abilityScores->equals(
                AbilityScores::average()
            )
        );
    }

    public function testEveryDefaultAbilityScoreIsTen(): void
    {
        $abilityScores = $this->rules()
            ->defaultAbilityScores();

        foreach ($abilityScores->all() as $abilityScore) {
            self::assertSame(
                10,
                $abilityScore->value()
            );
        }
    }

    public function testReturnsANewDefaultAbilityScoresInstanceEachTime(): void
    {
        $first = $this->rules()
            ->defaultAbilityScores();

        $second = $this->rules()
            ->defaultAbilityScores();

        self::assertNotSame(
            $first,
            $second
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testNewCharactersStartAtLevelOne(): void
    {
        self::assertTrue(
            $this->rules()
                ->startingLevel()
                ->equals(Level::one())
        );
    }

    public function testNewCharactersStartWithZeroExperience(): void
    {
        self::assertTrue(
            $this->rules()
                ->startingExperience()
                ->equals(Experience::zero())
        );
    }

    #[DataProvider('startingHitPointsProvider')]
    public function testCalculatesStartingHitPoints(
        string $characterClass,
        int $constitution,
        int $expectedHitPoints
    ): void {
        $abilityScores = AbilityScores::average()
            ->withConstitution(
                AbilityScore::fromInt(
                    $constitution
                )
            );

        self::assertSame(
            $expectedHitPoints,
            $this->rules()->startingHitPoints(
                CharacterClass::fromString(
                    $characterClass
                ),
                $abilityScores
            )
        );
    }

    /**
     * @return array<string,array{string,int,int}>
     */
    public static function startingHitPointsProvider(): array
    {
        return [
            'barbarian with constitution 16' => [
                'barbarian',
                16,
                15,
            ],
            'fighter with constitution 14' => [
                'fighter',
                14,
                12,
            ],
            'paladin with constitution 12' => [
                'paladin',
                12,
                11,
            ],
            'ranger with constitution 10' => [
                'ranger',
                10,
                10,
            ],
            'cleric with constitution 8' => [
                'cleric',
                8,
                7,
            ],
            'wizard with constitution 10' => [
                'wizard',
                10,
                6,
            ],
            'sorcerer with constitution 6' => [
                'sorcerer',
                6,
                4,
            ],
            'wizard with constitution 1' => [
                'wizard',
                1,
                1,
            ],
        ];
    }

    public function testStartingHitPointsUseConstitutionFromTheSuppliedCollection(): void
    {
        $abilityScores = AbilityScores::fromScores(
            strength: AbilityScore::fromInt(18),
            dexterity: AbilityScore::fromInt(16),
            constitution: AbilityScore::fromInt(14),
            intelligence: AbilityScore::fromInt(12),
            wisdom: AbilityScore::fromInt(10),
            charisma: AbilityScore::fromInt(8),
        );

        self::assertSame(
            12,
            $this->rules()->startingHitPoints(
                CharacterClass::fromString(
                    'fighter'
                ),
                $abilityScores
            )
        );
    }

    public function testStartingHitPointCalculationDoesNotModifyAbilityScores(): void
    {
        $abilityScores = AbilityScores::average()
            ->withConstitution(
                AbilityScore::fromInt(14)
            );

        $this->rules()->startingHitPoints(
            CharacterClass::fromString(
                'fighter'
            ),
            $abilityScores
        );

        self::assertSame(
            14,
            $abilityScores
                ->constitution()
                ->value()
        );
    }

    private function rules(): CharacterCreationRules
    {
        return new CharacterCreationRules();
    }
}
