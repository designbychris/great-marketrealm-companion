<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use PHPUnit\Framework\TestCase;

final class ClassProgressionCatalogueTest extends TestCase
{
    public function testCatalogueCoversEveryRegisteredCalling(): void
    {
        $catalogue =
            new ClassProgressionCatalogue();

        self::assertSame(
            CharacterClass::identifiers(),
            $catalogue->classes()
        );

        foreach (
            CharacterClass::identifiers()
            as $identifier
        ) {
            self::assertTrue(
                $catalogue->supports(
                    CharacterClass::fromString(
                        $identifier
                    )
                )
            );
        }
    }

    public function testWizardUsesReferenceProgression(): void
    {
        $entry = (
            new ClassProgressionCatalogue()
        )->forLevel(
            CharacterClass::fromString(
                'wizard'
            ),
            3
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );

        self::assertSame(
            'wizard',
            $entry['class']
        );

        self::assertSame(
            3,
            $entry['level']
        );

        self::assertSame(
            'spellbook',
            $entry['delegated'][0]['folio']
        );
    }

    public function testUnimportedCallingRemainsRegisteredWithoutInventedRules(): void
    {
        $entry = (
            new ClassProgressionCatalogue()
        )->forLevel(
            CharacterClass::fromString(
                'grocer'
            ),
            2
        );

        self::assertSame([], $entry['automatic']);
        self::assertSame([], $entry['delegated']);

        self::assertSame(
            'registered',
            $entry['catalogue_status']
        );
    }
}
