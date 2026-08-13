<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class ArcanePantrySpellbookContractTest extends TestCase
{
    public function testArcanePantryMarksCertifiedSpellbookEntries(): void
    {
        $root = dirname(__DIR__, 5);
        $presenter = file_get_contents(
            $root . '/app/Modules/Characters/Arcana/Services/ArcanePantryPresenter.php'
        );
        self::assertIsString($presenter);
        self::assertStringContainsString("'learned' => $learned", $presenter);
        self::assertStringContainsString("'spellbook' => $character->spellbook()->toArray()", $presenter);
    }
}
