<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class SpellbookCatalogueShortfallContractTest extends TestCase
{
    public function testSpellbookFolioSurfacesCatalogueShortfall(): void
    {
        $root = dirname(__DIR__, 5);

        $folio = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Folios/SpellbookFolio.php'
        );

        self::assertIsString($folio);

        self::assertStringContainsString(
            'catalogue_shortfall',
            $folio
        );

        self::assertStringContainsString(
            'The Guild cannot certify this advancement until the spell catalogue is restocked.',
            $folio
        );

        self::assertStringContainsString(
            'available_choices',
            $folio
        );
    }

    public function testChoiceResolverDoesNotReduceRequestedSpellCount(): void
    {
        $root = dirname(__DIR__, 5);

        $resolver = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Services/AdvancementChoiceRequirementResolver.php'
        );

        self::assertIsString($resolver);

        self::assertStringContainsString(
            'Do not silently weaken a progression rule',
            $resolver
        );
    }
}
