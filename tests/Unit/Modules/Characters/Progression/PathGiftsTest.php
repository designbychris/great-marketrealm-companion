<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use PHPUnit\Framework\TestCase;

final class PathGiftsTest extends TestCase
{
    public function testGiftsAreNormalisedAndDeduplicated(): void
    {
        $gifts = PathGifts::fromArray([
            'spell-stored-container',
            'Spell Stored Container',
            '',
        ]);

        self::assertSame(
            ['spell-stored-container'],
            $gifts->values()
        );
    }

    public function testGrantKeepsExistingGiftsAndAddsNewOnes(): void
    {
        $gifts = PathGifts::fromArray([
            'spell-stored-container',
        ])->grant([
            'packaging-proficiency',
            'spell-stored-container',
        ]);

        self::assertSame(
            [
                'spell-stored-container',
                'packaging-proficiency',
            ],
            $gifts->values()
        );

        self::assertTrue(
            $gifts->has('packaging-proficiency')
        );
    }
}
