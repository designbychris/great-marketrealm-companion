<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Skill;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SkillTest extends TestCase
{
    #[DataProvider('validModifierProvider')]
    public function testCreatesSkillFromModifier(
        int $modifier,
        bool $proficient,
        bool $expertise
    ): void {
        $skill = Skill::fromModifier(
            $modifier,
            $proficient,
            $expertise
        );

        self::assertSame(
            $modifier,
            $skill->modifier()
        );

        self::assertSame(
            $modifier,
            $skill->value()
        );

        self::assertSame(
            $proficient,
            $skill->isProficient()
        );

        self::assertSame(
            $expertise,
            $skill->hasExpertise()
        );
    }

    /**
     * @return array<string,array{int,bool,bool}>
     */
    public static function validModifierProvider(): array
    {
        return [
            'minimum' => [-20, false, false],
            'untrained' => [2, false, false],
            'proficient' => [5, true, false],
            'expertise' => [8, true, true],
            'maximum' => [40, true, true],
        ];
    }

    #[DataProvider('invalidModifierProvider')]
    public function testRejectsUnsupportedModifier(
        int $modifier
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Skill modifier must be between -20 and 40; received %d.',
                $modifier
            )
        );

        Skill::fromModifier($modifier);
    }

    /**
     * @return array<string,array{int}>
     */
    public static function invalidModifierProvider(): array
    {
        return [
            'below minimum' => [-21],
            'far below minimum' => [-100],
            'above maximum' => [41],
            'far above maximum' => [100],
        ];
    }

    public function testRejectsExpertiseWithoutProficiency(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'A Skill with expertise must also be proficient.'
        );

        Skill::fromModifier(
            modifier: 4,
            proficient: false,
            expertise: true
        );
    }

    #[DataProvider('untrainedAbilityProvider')]
    public function testCalculatesUntrainedSkill(
        int $abilityScore,
        int $expectedModifier
    ): void {
        $skill = Skill::fromAbility(
            AbilityScore::fromInt($abilityScore),
            ProficiencyBonus::fromInt(2)
        );

        self::assertSame(
            $expectedModifier,
            $skill->modifier()
        );

        self::assertFalse(
            $skill->isProficient()
        );

        self::assertFalse(
            $skill->hasExpertise()
        );
    }

    /**
     * @return array<string,array{int,int}>
     */
    public static function untrainedAbilityProvider(): array
    {
        return [
            'score 1' => [1, -5],
            'score 8' => [8, -1],
            'score 10' => [10, 0],
            'score 14' => [14, 2],
            'score 20' => [20, 5],
        ];
    }

    public function testAddsProficiencyBonus(): void
    {
        $skill = Skill::fromAbility(
            AbilityScore::fromInt(14),
            ProficiencyBonus::fromInt(3),
            proficient: true
        );

        self::assertSame(
            5,
            $skill->modifier()
        );

        self::assertTrue(
            $skill->isProficient()
        );

        self::assertFalse(
            $skill->hasExpertise()
        );
    }

    public function testExpertiseAddsTwiceProficiencyBonus(): void
    {
        $skill = Skill::fromAbility(
            AbilityScore::fromInt(14),
            ProficiencyBonus::fromInt(3),
            proficient: true,
            expertise: true
        );

        self::assertSame(
            8,
            $skill->modifier()
        );

        self::assertTrue(
            $skill->isProficient()
        );

        self::assertTrue(
            $skill->hasExpertise()
        );
    }

    public function testExpertiseAutomaticallyImpliesProficiency(): void
    {
        $skill = Skill::fromAbility(
            AbilityScore::fromInt(10),
            ProficiencyBonus::fromInt(2),
            proficient: false,
            expertise: true
        );

        self::assertTrue(
            $skill->isProficient()
        );

        self::assertTrue(
            $skill->hasExpertise()
        );

        self::assertSame(
            4,
            $skill->modifier()
        );
    }

    #[DataProvider('signedModifierProvider')]
    public function testFormatsSignedModifier(
        int $modifier,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            Skill::fromModifier(
                $modifier
            )->signed()
        );
    }

    /**
     * @return array<string,array{int,string}>
     */
    public static function signedModifierProvider(): array
    {
        return [
            'negative' => [-3, '-3'],
            'zero' => [0, '+0'],
            'positive' => [5, '+5'],
        ];
    }

    public function testEqualSkillsAreEqual(): void
    {
        self::assertTrue(
            Skill::fromModifier(
                5,
                true,
                false
            )->equals(
                Skill::fromModifier(
                    5,
                    true,
                    false
                )
            )
        );
    }

    public function testDifferentModifiersAreNotEqual(): void
    {
        self::assertFalse(
            Skill::fromModifier(4)->equals(
                Skill::fromModifier(5)
            )
        );
    }

    public function testDifferentProficiencyStatesAreNotEqual(): void
    {
        self::assertFalse(
            Skill::fromModifier(
                4,
                true
            )->equals(
                Skill::fromModifier(
                    4,
                    false
                )
            )
        );
    }

    public function testDifferentExpertiseStatesAreNotEqual(): void
    {
        self::assertFalse(
            Skill::fromModifier(
                4,
                true,
                true
            )->equals(
                Skill::fromModifier(
                    4,
                    true,
                    false
                )
            )
        );
    }

    public function testReturnsSupportedBounds(): void
    {
        self::assertSame(
            -20,
            Skill::minimum()
        );

        self::assertSame(
            40,
            Skill::maximum()
        );
    }

    public function testConvertsToSignedString(): void
    {
        self::assertSame(
            '+4',
            (string) Skill::fromModifier(4)
        );

        self::assertSame(
            '-2',
            (string) Skill::fromModifier(-2)
        );
    }

    public function testSkillIsImmutable(): void
    {
        $first = Skill::fromModifier(2);

        $second = Skill::fromModifier(
            6,
            true,
            true
        );

        self::assertSame(
            2,
            $first->modifier()
        );

        self::assertSame(
            6,
            $second->modifier()
        );

        self::assertNotSame(
            $first,
            $second
        );
    }
}
