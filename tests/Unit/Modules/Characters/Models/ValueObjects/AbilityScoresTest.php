<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use PHPUnit\Framework\TestCase;

final class AbilityScoresTest extends TestCase
{
    public function testCanCreateAverageAbilityScores(): void
    {
        $abilities = AbilityScores::average();

        foreach ($abilities->all() as $score) {
            self::assertSame(
                10,
                $score->value()
            );
        }
    }

    public function testCanBeCreatedFromSixScores(): void
    {
        $abilities = $this->customScores();

        self::assertSame(
            15,
            $abilities->strength()->value()
        );

        self::assertSame(
            14,
            $abilities->dexterity()->value()
        );

        self::assertSame(
            13,
            $abilities->constitution()->value()
        );

        self::assertSame(
            12,
            $abilities->intelligence()->value()
        );

        self::assertSame(
            10,
            $abilities->wisdom()->value()
        );

        self::assertSame(
            8,
            $abilities->charisma()->value()
        );
    }

    public function testReturnsStrengthScore(): void
    {
        self::assertSame(
            15,
            $this->customScores()
                ->strength()
                ->value()
        );
    }

    public function testReturnsDexterityScore(): void
    {
        self::assertSame(
            14,
            $this->customScores()
                ->dexterity()
                ->value()
        );
    }

    public function testReturnsConstitutionScore(): void
    {
        self::assertSame(
            13,
            $this->customScores()
                ->constitution()
                ->value()
        );
    }

    public function testReturnsIntelligenceScore(): void
    {
        self::assertSame(
            12,
            $this->customScores()
                ->intelligence()
                ->value()
        );
    }

    public function testReturnsWisdomScore(): void
    {
        self::assertSame(
            10,
            $this->customScores()
                ->wisdom()
                ->value()
        );
    }

    public function testReturnsCharismaScore(): void
    {
        self::assertSame(
            8,
            $this->customScores()
                ->charisma()
                ->value()
        );
    }

    public function testCanReplaceStrength(): void
    {
        $abilities = $this->customScores();

        $updated = $abilities->withStrength(
            AbilityScore::fromInt(18)
        );

        self::assertSame(
            18,
            $updated->strength()->value()
        );

        self::assertSame(
            15,
            $abilities->strength()->value()
        );
    }

    public function testCanReplaceDexterity(): void
    {
        $abilities = $this->customScores();

        $updated = $abilities->withDexterity(
            AbilityScore::fromInt(18)
        );

        self::assertSame(
            18,
            $updated->dexterity()->value()
        );

        self::assertSame(
            14,
            $abilities->dexterity()->value()
        );
    }

    public function testCanReplaceConstitution(): void
    {
        $abilities = $this->customScores();

        $updated = $abilities->withConstitution(
            AbilityScore::fromInt(18)
        );

        self::assertSame(
            18,
            $updated->constitution()->value()
        );

        self::assertSame(
            13,
            $abilities->constitution()->value()
        );
    }

    public function testCanReplaceIntelligence(): void
    {
        $abilities = $this->customScores();

        $updated = $abilities->withIntelligence(
            AbilityScore::fromInt(18)
        );

        self::assertSame(
            18,
            $updated->intelligence()->value()
        );

        self::assertSame(
            12,
            $abilities->intelligence()->value()
        );
    }

    public function testCanReplaceWisdom(): void
    {
        $abilities = $this->customScores();

        $updated = $abilities->withWisdom(
            AbilityScore::fromInt(18)
        );

        self::assertSame(
            18,
            $updated->wisdom()->value()
        );

        self::assertSame(
            10,
            $abilities->wisdom()->value()
        );
    }

    public function testCanReplaceCharisma(): void
    {
        $abilities = $this->customScores();

        $updated = $abilities->withCharisma(
            AbilityScore::fromInt(18)
        );

        self::assertSame(
            18,
            $updated->charisma()->value()
        );

        self::assertSame(
            8,
            $abilities->charisma()->value()
        );
    }

    public function testReplacingOneScorePreservesTheOtherScores(): void
    {
        $abilities = $this->customScores();

        $updated = $abilities->withStrength(
            AbilityScore::fromInt(18)
        );

        self::assertSame(
            14,
            $updated->dexterity()->value()
        );

        self::assertSame(
            13,
            $updated->constitution()->value()
        );

        self::assertSame(
            12,
            $updated->intelligence()->value()
        );

        self::assertSame(
            10,
            $updated->wisdom()->value()
        );

        self::assertSame(
            8,
            $updated->charisma()->value()
        );
    }

    public function testEqualAbilityScoreCollectionsAreEqual(): void
    {
        self::assertTrue(
            $this->customScores()->equals(
                $this->customScores()
            )
        );
    }

    public function testDifferentAbilityScoreCollectionsAreNotEqual(): void
    {
        $first = $this->customScores();

        $second = $first->withCharisma(
            AbilityScore::fromInt(9)
        );

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testReturnsAllScoresKeyedByAbilityName(): void
    {
        $all = $this->customScores()->all();

        self::assertSame(
            [
                'strength',
                'dexterity',
                'constitution',
                'intelligence',
                'wisdom',
                'charisma',
            ],
            array_keys($all)
        );

        self::assertContainsOnlyInstancesOf(
            AbilityScore::class,
            $all
        );
    }

    public function testAllReturnsTheCorrectScores(): void
    {
        $all = $this->customScores()->all();

        self::assertSame(
            15,
            $all['strength']->value()
        );

        self::assertSame(
            14,
            $all['dexterity']->value()
        );

        self::assertSame(
            13,
            $all['constitution']->value()
        );

        self::assertSame(
            12,
            $all['intelligence']->value()
        );

        self::assertSame(
            10,
            $all['wisdom']->value()
        );

        self::assertSame(
            8,
            $all['charisma']->value()
        );
    }

    /**
     * Create the standard custom scores used by this test suite.
     */
    private function customScores(): AbilityScores
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
