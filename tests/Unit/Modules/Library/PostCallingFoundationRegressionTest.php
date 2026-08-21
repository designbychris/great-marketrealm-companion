<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use GreatMarketrealmCompanion\Kingdoms\KingdomRegistry;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Catalogues\ArmouryReferenceCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Catalogues\BackgroundReferenceCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Catalogues\SpellReferenceCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Catalogues\RelicReferenceCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Models\ReferenceLibraryRegistry;
use GreatMarketrealmCompanion\Navigation\Navigation;
use PHPUnit\Framework\TestCase;

final class PostCallingFoundationRegressionTest extends TestCase
{
    public function testFoundationRegistersFourReferenceDomains(): void
    {
        $registry = $this->registry();

        self::assertSame(4, $registry->count());
        self::assertSame(
            [
                'spells',
                'backgrounds',
                'armoury',
                'relics',
            ],
            array_column(
                $registry->summaries(),
                'key'
            )
        );
    }

    public function testEveryDomainUsesPlayersHandbookAsCanonicalSource(): void
    {
        foreach (
            $this->registry()->summaries()
            as $domain
        ) {
            self::assertSame(
                'The Great Marketrealm - Players Handbook',
                $domain['canonical_source']
            );
        }
    }

    public function testFoundationDoesNotImportRecordsEarly(): void
    {
        foreach (
            $this->registry()->summaries()
            as $domain
        ) {
            self::assertSame(
                'registered',
                $domain['status']
            );
            self::assertGreaterThan(
                0,
                $domain['entry_count']
            );
        }
    }

    public function testDedicatedPostCallingPhasesRemainExplicit(): void
    {
        self::assertSame(
            [
                'III.13.1A',
                'III.13.3',
                'III.13.4',
                'III.13.5',
            ],
            array_column(
                $this->registry()->summaries(),
                'phase'
            )
        );
    }

    public function testRegistryRejectsDuplicateCatalogueKeys(): void
    {
        $registry =
            new ReferenceLibraryRegistry();

        $registry->add(
            new SpellReferenceCatalogue()
        );

        $this->expectException(
            \InvalidArgumentException::class
        );

        $registry->add(
            new SpellReferenceCatalogue()
        );
    }

    public function testLibraryKingdomIsInstalledByApplicationRegistry(): void
    {
        $source = $this->source(
            'app/Providers/KingdomServiceProvider.php'
        );

        self::assertStringContainsString(
            'LibraryKingdom',
            $source
        );

        self::assertStringContainsString(
            'new LibraryKingdom($this->app)',
            $source
        );
    }

    public function testLibraryKingdomContributesRealRouteAndNavigation(): void
    {
        $kingdom = $this->source(
            'app/Kingdoms/LibraryKingdom.php'
        );

        self::assertStringContainsString(
            "return 'library';",
            $kingdom
        );
        self::assertStringContainsString(
            "app/Modules/Library/Routes.php",
            $kingdom
        );
        self::assertStringContainsString(
            "'Guild Library'",
            $kingdom
        );
        self::assertStringContainsString(
            'Icons::BOOK',
            $kingdom
        );
    }

    public function testLibraryRouteTargetsFoundationController(): void
    {
        $route = $this->source(
            'app/Modules/Library/Routes.php'
        );

        self::assertStringContainsString(
            "'/library'",
            $route
        );
        self::assertStringContainsString(
            'LibraryController::class',
            $route
        );
    }

    public function testLibraryControllerProjectsRegistrySummariesOnly(): void
    {
        $controller = $this->source(
            'app/Modules/Library/Controllers/'
            . 'LibraryController.php'
        );

        self::assertStringContainsString(
            "'library.index'",
            $controller
        );
        self::assertStringContainsString(
            '$this->library->summaries()',
            $controller
        );
    }

    public function testFoundationPageNamesAllThreeFutureLibraries(): void
    {
        $view = $this->source(
            'app/Modules/Library/Views/index.php'
        );

        self::assertStringContainsString(
            'The Guild Library',
            $view
        );
        self::assertStringContainsString(
            'Canonical source',
            $view
        );
        self::assertStringContainsString(
            'data-library-domain',
            $view
        );
        self::assertStringContainsString(
            'Records remain intentionally untouched',
            $view
        );
    }

    public function testGuildLibraryPresentationIsResponsiveAndHighContrastSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/library/'
            . 'guild-library.css'
        );

        self::assertStringContainsString(
            '.gmrc-guild-library__grid',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 900px)',
            $css
        );
        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
        self::assertStringContainsString(
            'overflow-wrap: anywhere',
            $css
        );
    }

    public function testExistingThirteenSpecialistCallingsRemainCertified(): void
    {
        $catalogue =
            new ClassCapabilityCatalogue();

        self::assertCount(
            13,
            $catalogue->specialist()
        );

        self::assertSame(
            'specialist',
            $catalogue
                ->forClass(
                    CharacterClass::fromString(
                        'artificer'
                    )
                )
                ->implementationState()
        );
    }

    public function testFoundationCataloguesRemainIndependentOfCharacterCatalogues(): void
    {
        foreach ([
            SpellReferenceCatalogue::class,
            BackgroundReferenceCatalogue::class,
            ArmouryReferenceCatalogue::class,
            RelicReferenceCatalogue::class,
        ] as $catalogue) {
            $source = $this->source(
                str_replace(
                    [
                        'GreatMarketrealmCompanion\\',
                        '\\',
                    ],
                    [
                        'app/',
                        '/',
                    ],
                    $catalogue
                )
                . '.php'
            );

            self::assertStringNotContainsString(
                'ArcaneAbilityCatalogue',
                $source
            );
            self::assertStringNotContainsString(
                'ItemCatalogue',
                $source
            );
            self::assertStringNotContainsString(
                'CharacterController',
                $source
            );
        }
    }

    private function registry(): ReferenceLibraryRegistry
    {
        $registry =
            new ReferenceLibraryRegistry();

        $registry->add(
            new SpellReferenceCatalogue()
        );
        $registry->add(
            new BackgroundReferenceCatalogue()
        );
        $registry->add(
            new ArmouryReferenceCatalogue()
        );
        $registry->add(
            new RelicReferenceCatalogue()
        );

        return $registry;
    }

    private function source(
        string $relative
    ): string {
        $source =
            file_get_contents(
                $this->root()
                . '/'
                . $relative
            );

        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 4);
    }
}
