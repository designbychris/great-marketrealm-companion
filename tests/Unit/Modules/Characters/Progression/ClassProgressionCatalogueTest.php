<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use PHPUnit\Framework\TestCase;

final class ClassProgressionCatalogueTest extends TestCase
{
    public function testFoundationCatalogueCoversEveryRegisteredClass(): void
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
                    CharacterClass::fromString($identifier)
                )
            );
        }
    }

    public function testFoundationEntryDoesNotInventClassRules(): void
    {
        $entry = (
            new ClassProgressionCatalogue()
        )->forLevel(
            CharacterClass::fromString('wizard'),
            2
        );

        self::assertSame([], $entry['automatic']);
        self::assertSame([], $entry['choices']);
        self::assertSame(
            'foundation',
            $entry['catalogue_status']
        );
    }
}
