<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Standard;

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyStandard;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FellowshipStandardRegressionTest extends TestCase
{
    public function testNewFellowshipReceivesDeterministicDefaultStandard(): void
    {
        $party = Party::create(
            PartyId::generate(),
            PartyName::fromString('Test Fellowship'),
            PartyOwnerId::fromInt(42)
        );

        self::assertSame(
            'aubergine-gold',
            $party->standard()->palette()
        );
        self::assertSame(
            'guild-star',
            $party->standard()->emblem()
        );
        self::assertSame(
            'flourish',
            $party->standard()->ornament()
        );
    }

    public function testStandardAcceptsOnlyCatalogueBackedIdentityValues(): void
    {
        $standard = PartyStandard::make(
            'frost-blue',
            'market-leaf',
            'stars'
        );

        self::assertSame('frost-blue', $standard->palette());
        self::assertSame('market-leaf', $standard->emblem());
        self::assertSame('stars', $standard->ornament());

        $this->expectException(InvalidArgumentException::class);

        PartyStandard::make(
            'url(javascript:bad)',
            'market-leaf',
            'stars'
        );
    }

    public function testRepositoryPersistsStandardAsDedicatedMeta(): void
    {
        $root = dirname(__DIR__, 5);
        $repository = file_get_contents(
            $root
            . '/app/Modules/Parties/Repositories/'
            . 'PartyRepository.php'
        );

        self::assertIsString($repository);
        self::assertStringContainsString(
            "'_gmrc_party_standard'",
            $repository
        );
        self::assertStringContainsString(
            '$party->standard()->toArray()',
            $repository
        );
        self::assertStringContainsString(
            '$this->standard($post->ID)',
            $repository
        );
        self::assertStringContainsString(
            'PartyStandard::default()',
            $repository
        );
    }

    public function testStandardHasDedicatedOwnerScopedHttpAction(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = file_get_contents(
            $root . '/app/Modules/Parties/Routes.php'
        );
        $controller = file_get_contents(
            $root
            . '/app/Modules/Parties/Controllers/'
            . 'PartyController.php'
        );
        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($routes);
        self::assertIsString($controller);
        self::assertIsString($provider);
        self::assertStringContainsString(
            "'/parties/{id}/standard'",
            $routes
        );
        self::assertStringContainsString(
            "'updateStandard'",
            $routes
        );
        self::assertStringContainsString(
            '$this->updateStandard->handle(',
            $controller
        );
        self::assertStringContainsString(
            '#^parties/([^/]+)/standard$#',
            $provider
        );
        self::assertStringContainsString(
            "'gmrc_party_'",
            $provider
        );
    }

    public function testEditViewOffersPaletteEmblemAndOrnamentCatalogues(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Parties/Views/edit.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'The Fellowship Standard',
            $view
        );
        self::assertStringContainsString(
            'name="palette"',
            $view
        );
        self::assertStringContainsString(
            'name="emblem"',
            $view
        );
        self::assertStringContainsString(
            'name="ornament"',
            $view
        );
        self::assertStringContainsString(
            'Save Fellowship Standard',
            $view
        );
    }

    public function testCompanyPortraitIsContainedInsideItsGridColumn(): void
    {
        $root = dirname(__DIR__, 5);
        $css = file_get_contents(
            $root
            . '/assets/css/modules/parties/'
            . 'fellowship-register.css'
        );

        self::assertIsString($css);
        self::assertStringContainsString(
            '.gmrc-fellowship-hero__portrait {',
            $css
        );
        self::assertStringContainsString(
            'min-width: 0;',
            $css
        );
        self::assertStringContainsString(
            'max-width: 100%;',
            $css
        );
        self::assertStringContainsString(
            'overflow: hidden;',
            $css
        );
        self::assertStringContainsString(
            'isolation: isolate;',
            $css
        );
        self::assertStringContainsString(
            'height: auto;',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-fellowship-hero__copy {',
            $css
        );
    }

    public function testStandardPaletteDecoratesHallAndRegisterWithoutChangingPortraitRecipe(): void
    {
        $root = dirname(__DIR__, 5);
        $show = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );
        $index = file_get_contents(
            $root . '/app/Modules/Parties/Views/index.php'
        );
        $portrait = file_get_contents(
            $root
            . '/app/Views/components/media/'
            . 'fellowship-portrait.php'
        );

        self::assertIsString($show);
        self::assertIsString($index);
        self::assertIsString($portrait);
        self::assertStringContainsString(
            'data-standard-palette',
            $show
        );
        self::assertStringContainsString(
            'data-standard-palette',
            $index
        );
        self::assertStringContainsString(
            'gmrc-fellowship-standard-seal',
            $show
        );
        self::assertStringContainsString(
            'gmrc-fellowship-standard-seal',
            $index
        );
        self::assertStringNotContainsString(
            'PartyStandard',
            $portrait
        );
    }
}
