<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use PHPUnit\Framework\TestCase;

final class ShelfmancyGiftProgressionTest extends TestCase
{
    public function testShelfmancyUsesHandbookMilestones(): void
    {
        $gifts = (
            new PathGiftCatalogue()
        )->all(
            'school-of-shelfmancy'
        );

        self::assertSame(
            [2, 2, 6, 10, 14],
            array_column(
                $gifts,
                'level'
            )
        );

        self::assertSame(
            [
                'spell-stored-container',
                'packaging-proficiency',
                'vacuum-lock',
                'dimensional-pantry',
                'master-of-the-endless-aisles',
            ],
            array_column(
                $gifts,
                'key'
            )
        );
    }

    public function testUnlockedExcludesAlreadyCertifiedGifts(): void
    {
        $gifts = (
            new PathGiftCatalogue()
        )->unlocked(
            'school-of-shelfmancy',
            6,
            PathGifts::fromArray([
                'spell-stored-container',
                'packaging-proficiency',
            ])
        );

        self::assertSame(
            ['vacuum-lock'],
            array_column(
                $gifts,
                'key'
            )
        );
    }
}
