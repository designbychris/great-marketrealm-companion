<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Spellbook;
use PHPUnit\Framework\TestCase;

final class SpellbookTest extends TestCase
{
    public function testSpellbookLearnsAndDeduplicatesArcana(): void
    {
        $book = Spellbook::empty()->learn(
            ['pantry-ward', 'pantry-ward'],
            ['produce-spark']
        );

        self::assertSame(['pantry-ward'], $book->spells());
        self::assertSame(['produce-spark'], $book->cantrips());
        self::assertTrue($book->knows('pantry-ward'));
    }
}
