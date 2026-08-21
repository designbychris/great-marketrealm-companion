<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use GreatMarketrealmCompanion\Modules\Library\Catalogues\RelicReferenceCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Relics\Repositories\HandbookRelicRegister;
use GreatMarketrealmCompanion\Modules\Library\Relics\Services\RelicRegisterPresenter;
use PHPUnit\Framework\TestCase;

final class RelicsOfMarketrealmRegressionTest extends TestCase
{
    public function testHandbookImportsNineteenRelicRecords(): void
    {
        self::assertCount(19, (new HandbookRelicRegister())->all());
    }

    public function testLegendaryShelfContainsCanonicalArmourAndWeapons(): void
    {
        $register = new HandbookRelicRegister();

        self::assertSame('Legendary', $register->find('armor-verdant-vitality')?->rarity());
        self::assertSame('Legendary', $register->find('prime-cutplate')?->rarity());
        self::assertSame('Legendary', $register->find('fruitcore-aegis')?->rarity());
        self::assertSame('Legendary', $register->find('skewer-sovereign-grill')?->rarity());
        self::assertSame('Legendary', $register->find('blade-dishlord')?->rarity());
    }

    public function testMagicalFoilVariantsRemainDistinctRecords(): void
    {
        $register = new HandbookRelicRegister();

        foreach ([
            'foilwrap-flamewarden',
            'chillwrap-foil',
            'crackfoil-sparked-chef',
            'prismatic-foilwrap',
        ] as $key) {
            self::assertSame('magical-armour', $register->find($key)?->group());
        }
    }

    public function testAttunementRestrictionsArePreserved(): void
    {
        $register = new HandbookRelicRegister();

        self::assertSame(
            'Requires attunement by a Druid or Ranger',
            $register->find('armor-verdant-vitality')?->attunement()
        );
        self::assertSame(
            'Meat-only; requires attunement',
            $register->find('prime-cutplate')?->attunement()
        );
        self::assertSame(
            'Attunement optional',
            $register->find('fruitcore-aegis')?->attunement()
        );
    }

    public function testPresenterFiltersByRarityGroupAndSearch(): void
    {
        $presenter = new RelicRegisterPresenter();

        $legendary = $presenter->present(['rarity' => 'Legendary']);
        self::assertSame(5, $legendary['result_count']);

        $foil = $presenter->present(['group' => 'magical-armour']);
        self::assertSame(4, $foil['result_count']);

        $garlic = $presenter->present(['q' => 'radiant freshness']);
        self::assertSame(1, $garlic['result_count']);
        self::assertSame('Garlic Orb of Anti-Foulness', $garlic['results'][0]['name']);
    }

    public function testRelicCatalogueIsRegisteredForPhaseThirteenFive(): void
    {
        $catalogue = new RelicReferenceCatalogue();

        self::assertSame('relics', $catalogue->key());
        self::assertSame('III.13.5', $catalogue->phase());
        self::assertSame('registered', $catalogue->status());
        self::assertCount(19, $catalogue->entries());
    }

    public function testGuildLibraryExposesRelicRouteAndOpenLink(): void
    {
        $routes = $this->source('app/Modules/Library/Routes.php');
        $landing = $this->source('app/Modules/Library/Views/index.php');

        self::assertStringContainsString("'/library/relics'", $routes);
        self::assertStringContainsString('Open Relic Register', $landing);
    }

    public function testRelicViewDoesNotPretendSpecialPowersAreInventoryAutomation(): void
    {
        $view = $this->source('app/Modules/Library/Views/relics/index.php');

        self::assertStringContainsString('reference entries only', $view);
        self::assertStringContainsString('not', $view);
        self::assertStringContainsString('silently automated', $view);
    }

    public function testLibraryArtworkIsWiredIntoPresentation(): void
    {
        $css = $this->source('assets/css/modules/library/guild-library.css');

        self::assertStringContainsString('guild-library-background.png', $css);
        self::assertStringContainsString('guild-library-auby.png', $css);
        self::assertStringContainsString('@media (forced-colors: active)', $css);
    }

    public function testRelicPhaseDoesNotModifyCharacterInventoryPersistence(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Inventory/Models/CharacterInventory.php'
        );

        self::assertStringNotContainsString('HandbookRelicRegister', $source);
        self::assertStringNotContainsString('RelicRecord', $source);
    }


    public function testLibraryBackdropIsScopedBelowGlobalNavigation(): void
    {
        $css = $this->source(
            'assets/css/modules/library/guild-library.css'
        );

        self::assertStringContainsString(
            '.gmrc-content:has(> .gmrc-guild-library)',
            $css
        );
        self::assertStringContainsString(
            'guild-library-background.png',
            $css
        );
        self::assertStringNotContainsString(
            '.gmrc-app-main:has(.gmrc-guild-library)',
            $css
        );
        self::assertStringNotContainsString(
            '.gmrc-guild-library::before',
            $css
        );
    }


    public function testLibraryBackdropLivesBelowNavigationInsteadOfOnViewport(): void
    {
        $css = $this->source(
            'assets/css/modules/library/guild-library.css'
        );
        $navigation = $this->source(
            'assets/css/components/navigation/guild-navigation.css'
        );

        self::assertStringContainsString(
            '.gmrc-content:has(> .gmrc-relics)',
            $css
        );
        self::assertStringNotContainsString(
            '.gmrc-relics::before',
            $css
        );
        self::assertStringContainsString(
            'z-index: 50;',
            $navigation
        );
    }


    public function testEachLibraryRoomUsesItsApprovedArtworkWithoutRelicDuplication(): void
    {
        $css = $this->source(
            'assets/css/modules/library/guild-library.css'
        );

        self::assertStringContainsString(
            'guild-library-background.png',
            $css
        );
        self::assertStringContainsString(
            'guild-library-auby.png',
            $css
        );
        self::assertStringContainsString(
            'guild-library-sage.png',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-content:has(> .gmrc-relics)',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-content:has(> .gmrc-spellbook)',
            $css
        );

        $relicHeroStart = strpos(
            $css,
            '.gmrc-relics__hero {'
        );
        self::assertIsInt($relicHeroStart);

        $relicHeroEnd = strpos(
            $css,
            '}',
            $relicHeroStart
        );
        self::assertIsInt($relicHeroEnd);

        $relicHero = substr(
            $css,
            $relicHeroStart,
            $relicHeroEnd - $relicHeroStart
        );

        self::assertStringNotContainsString(
            'guild-library-auby.png',
            $relicHero
        );
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root() . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 4);
    }
}
