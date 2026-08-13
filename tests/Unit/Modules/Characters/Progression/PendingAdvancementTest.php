<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\PendingAdvancement;
use PHPUnit\Framework\TestCase;

final class PendingAdvancementTest extends TestCase
{
    public function testPendingAdvancementRecordsChoicesWithoutCharacterMutation(): void
    {
        $id = CharacterId::fromString(
            '01KZM4W72K1G12FY75R0BTQREW'
        );

        $pending = PendingAdvancement::begin(
            $id,
            1,
            2
        );

        $pending->recordChoice(
            'vitality-hit-points',
            ['average']
        );

        self::assertTrue(
            $pending->matches(1, 2)
        );

        self::assertSame(
            [
                'vitality-hit-points' => [
                    'average',
                ],
            ],
            $pending->choices()
        );

        self::assertSame(
            1,
            $pending->toArray()['schema_version']
        );
    }

    public function testPendingAdvancementCanBeRestoredFromStoredState(): void
    {
        $id = CharacterId::fromString(
            '01KZM4W72K1G12FY75R0BTQREW'
        );

        $restored = PendingAdvancement::fromArray(
            $id,
            [
                'schema_version' => 1,
                'character_id' => $id->value(),
                'from_level' => 1,
                'target_level' => 2,
                'choices' => [
                    'vitality-hit-points' => [
                        'roll',
                    ],
                ],
            ]
        );

        self::assertInstanceOf(
            PendingAdvancement::class,
            $restored
        );

        self::assertSame(
            ['roll'],
            $restored->choices()[
                'vitality-hit-points'
            ]
        );
    }
}
