<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Arcana;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityDefinition;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Services\ArcaneRollScalingResolver;
use PHPUnit\Framework\TestCase;

final class ArcaneRollScalingResolverTest extends TestCase
{
    public function testCharacterLevelScalingUsesHighestEligibleThreshold(): void
    {
        $ability = new ArcaneAbilityDefinition(
            'spark',
            'Spark',
            'cantrip',
            ['wizard'],
            'Test',
            '1 action',
            '60 ft',
            'Instantaneous',
            'At will',
            'damage',
            '1d10',
            characterLevelScaling: [
                1 => '1d10',
                5 => '2d10',
                11 => '3d10',
                17 => '4d10',
            ]
        );

        $resolved = (new ArcaneRollScalingResolver())->resolve(
            $ability,
            11
        );

        self::assertSame('3d10', $resolved['formula']);
        self::assertSame('1d10', $resolved['base_formula']);
        self::assertSame('character-level', $resolved['source']);
        self::assertSame(11, $resolved['resolved_at']);
        self::assertTrue($resolved['scalable']);
    }

    public function testSlotLevelScalingCanResolveFutureHigherSlotFormula(): void
    {
        $ability = new ArcaneAbilityDefinition(
            'missile',
            'Missile',
            'spell',
            ['wizard'],
            'Test',
            '1 action',
            '120 ft',
            'Instantaneous',
            '1st-level slot',
            'damage',
            '3d4',
            spellLevel: 1,
            slotLevelScaling: [
                1 => '3d4',
                2 => '4d4',
                3 => '5d4',
            ]
        );

        $resolved = (new ArcaneRollScalingResolver())->resolve(
            $ability,
            5,
            3
        );

        self::assertSame('5d4', $resolved['formula']);
        self::assertSame('slot-level', $resolved['source']);
        self::assertSame(3, $resolved['resolved_at']);
        self::assertSame(
            [1 => '3d4', 2 => '4d4', 3 => '5d4'],
            $resolved['slot_options']
        );
    }

    public function testFeatureRankAxisIsPreparedForFutureRankedFeatures(): void
    {
        $ability = new ArcaneAbilityDefinition(
            'ranked-feature',
            'Ranked Feature',
            'feature',
            ['fighter'],
            'Test',
            '1 action',
            'Self',
            'Instantaneous',
            'At will',
            'damage',
            '1d6',
            featureRankScaling: [
                1 => '1d6',
                2 => '2d6',
                3 => '3d6',
            ]
        );

        $resolved = (new ArcaneRollScalingResolver())->resolve(
            $ability,
            5,
            null,
            2
        );

        self::assertSame('2d6', $resolved['formula']);
        self::assertSame('feature-rank', $resolved['source']);
        self::assertSame(2, $resolved['resolved_at']);
    }

    public function testInvalidScalingRulesFallBackToBaseFormula(): void
    {
        $ability = new ArcaneAbilityDefinition(
            'oddity',
            'Oddity',
            'feature',
            ['wizard'],
            'Test',
            '1 action',
            'Self',
            'Instantaneous',
            'At will',
            'damage',
            '1d8',
            characterLevelScaling: [
                5 => 'not-a-formula',
            ]
        );

        $resolved = (new ArcaneRollScalingResolver())->resolve(
            $ability,
            10
        );

        self::assertSame('1d8', $resolved['formula']);
        self::assertSame('base', $resolved['source']);
        self::assertNull($resolved['resolved_at']);
        self::assertTrue($resolved['scalable']);
    }
}
