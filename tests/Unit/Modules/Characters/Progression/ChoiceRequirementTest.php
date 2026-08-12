<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceMode;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceRequirement;
use PHPUnit\Framework\TestCase;

final class ChoiceRequirementTest extends TestCase
{
    public function testSingleChoiceAcceptsOneAllowedSelection(): void
    {
        $requirement = new ChoiceRequirement(
            'vitality-hit-points',
            ChoiceMode::SINGLE,
            [
                'average',
                'roll',
            ]
        );

        self::assertTrue(
            $requirement->satisfiedBy(
                ['average']
            )
        );

        self::assertFalse(
            $requirement->satisfiedBy([])
        );

        self::assertSame(
            ['roll'],
            $requirement->normalise(
                [
                    'unknown',
                    'roll',
                    'average',
                ]
            )
        );
    }

    public function testChooseNRequiresConfiguredCardinality(): void
    {
        $requirement = new ChoiceRequirement(
            'future-spells',
            ChoiceMode::CHOOSE_N,
            [
                'spell-a',
                'spell-b',
                'spell-c',
            ],
            2,
            2
        );

        self::assertFalse(
            $requirement->satisfiedBy(
                ['spell-a']
            )
        );

        self::assertTrue(
            $requirement->satisfiedBy(
                [
                    'spell-a',
                    'spell-b',
                ]
            )
        );
    }
}
