<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Language;
use PHPUnit\Framework\TestCase;

final class MarketrealmLanguageCatalogueTest extends TestCase
{
    public function testLanguageCatalogueContainsOnlyMarketrealmLanguages(): void
    {
        $languages = array_map(
            static fn (Language $language): string =>
                $language->label(),
            Language::all()
        );

        self::assertSame(
            [
                'Common',
                'Fructan',
                'Vegcant',
                'Mycelian',
                'Dairy Tongue',
                'Meat Speech',
                'Shelf Script',
                'PieSpeak',
            ],
            $languages
        );
    }

    public function testLegacyFantasyLanguagesCannotEnterGeneratorOptions(): void
    {
        foreach (
            [
                'Dwarvish',
                'Elvish',
                'Giant',
                'Gnomish',
                'Goblin',
                'Halfling',
                'Orc',
            ]
            as $legacyLanguage
        ) {
            self::assertFalse(
                Language::supports(
                    $legacyLanguage
                )
            );
        }
    }

    public function testCreateAndEditUseAuthoritativeLanguageCatalogue(): void
    {
        $root = dirname(__DIR__, 5);

        foreach (
            [
                '/app/Modules/Characters/Views/create.php',
                '/app/Modules/Characters/Views/edit.php',
            ]
            as $relative
        ) {
            $view = file_get_contents(
                $root . $relative
            );

            self::assertIsString($view);

            self::assertStringContainsString(
                '$languageOptions = Language::all();',
                $view
            );
        }
    }
}
