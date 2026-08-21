<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use GreatMarketrealmCompanion\Modules\Library\Spells\Services\SpellbookPresenter;
use PHPUnit\Framework\TestCase;

final class SagesSpellbookRegressionTest extends TestCase
{
    public function testUnfilteredSpellbookProjectsAllSeventyOneSpells(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present();

        self::assertSame(
            71,
            $spellbook['total_count']
        );
        self::assertSame(
            71,
            $spellbook['result_count']
        );
        self::assertCount(
            71,
            $spellbook['results']
        );
    }

    public function testSpellbookKeepsCertifiedRegisterSummaryCounts(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present();

        self::assertSame(
            29,
            $spellbook['renamed_count']
        );
        self::assertSame(
            42,
            $spellbook['original_count']
        );
        self::assertGreaterThan(
            0,
            $spellbook['source_issue_count']
        );
    }

    public function testSearchFindsMarketrealmNameCaseInsensitively(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'q' => 'CURE MEATS',
        ]);

        self::assertSame(
            ['Cure Meats'],
            array_column(
                $spellbook['results'],
                'name'
            )
        );
    }

    public function testSearchAlsoFindsOriginalSpellName(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'q' => 'Cure Wounds',
        ]);

        self::assertContains(
            'Cure Meats',
            array_column(
                $spellbook['results'],
                'name'
            )
        );
    }

    public function testKindFilterSeparatesRenamedAndOriginalMagic(): void
    {
        $renamed = (
            new SpellbookPresenter()
        )->present([
            'kind' => 'renamed',
        ]);

        $original = (
            new SpellbookPresenter()
        )->present([
            'kind' =>
                'marketrealm-original',
        ]);

        self::assertSame(
            29,
            $renamed['result_count']
        );
        self::assertSame(
            42,
            $original['result_count']
        );

        self::assertSame(
            ['renamed'],
            array_values(
                array_unique(
                    array_column(
                        $renamed['results'],
                        'kind'
                    )
                )
            )
        );
    }

    public function testLevelFilterIncludesCantripsWithoutInventingUnknownLevels(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'level' => '0',
        ]);

        self::assertGreaterThan(
            0,
            $spellbook['result_count']
        );

        foreach (
            $spellbook['results']
            as $spell
        ) {
            self::assertSame(
                0,
                $spell['level']
            );
            self::assertSame(
                'Cantrip',
                $spell['level_label']
            );
        }
    }

    public function testSchoolFilterUsesOnlyStatedHandbookSchools(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'school' => 'evocation',
        ]);

        self::assertGreaterThan(
            0,
            $spellbook['result_count']
        );

        foreach (
            $spellbook['results']
            as $spell
        ) {
            self::assertSame(
                'evocation',
                $spell['school']
            );
        }
    }

    public function testAccessFilterPreservesSourceLabelRatherThanCorrectingIt(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'access' => 'Arificer',
        ]);

        self::assertContains(
            'Oven of Annihilation',
            array_column(
                $spellbook['results'],
                'name'
            )
        );
    }

    public function testConflictingBreadWallVariantsRemainExpandableTogether(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'q' => 'Bread Wall',
        ]);

        $breadWall = array_values(
            array_filter(
                $spellbook['results'],
                static fn (array $spell): bool =>
                    ($spell['key'] ?? '')
                    === 'bread-wall'
            )
        )[0];

        self::assertSame(
            2,
            $breadWall['variant_count']
        );
        self::assertCount(
            2,
            $breadWall['variants']
        );
    }

    public function testVacuumSealStillCarriesItsTwoDifferentSourceLevels(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'q' => 'Vacuum Seal',
        ]);

        $vacuum = array_values(
            array_filter(
                $spellbook['results'],
                static fn (array $spell): bool =>
                    ($spell['key'] ?? '')
                    === 'vacuum-seal'
            )
        )[0];

        self::assertSame(
            [3, 4],
            array_column(
                $vacuum['variants'],
                'level'
            )
        );
    }

    public function testNoResultSearchReturnsSafeEmptyStateData(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'q' =>
                'definitely-not-a-marketrealm-spell',
        ]);

        self::assertSame(
            0,
            $spellbook['result_count']
        );
        self::assertSame(
            [],
            $spellbook['results']
        );
        self::assertSame(
            71,
            $spellbook['total_count']
        );
    }

    public function testSpellbookRouteAndControllerAreDedicatedLibrarySurfaces(): void
    {
        $routes = $this->source(
            'app/Modules/Library/Routes.php'
        );
        $controller = $this->source(
            'app/Modules/Library/Controllers/'
            . 'LibraryController.php'
        );

        self::assertStringContainsString(
            "'/library/spells'",
            $routes
        );
        self::assertStringContainsString(
            "[LibraryController::class, 'spells']",
            $routes
        );
        self::assertStringContainsString(
            "'library.spells.index'",
            $controller
        );
        self::assertStringContainsString(
            'SpellbookPresenter',
            $controller
        );
    }

    public function testGuildLibraryOpensRegisteredSpellbook(): void
    {
        $view = $this->source(
            'app/Modules/Library/Views/index.php'
        );

        self::assertStringContainsString(
            'Open Sage’s Spellbook',
            $view
        );
        self::assertStringContainsString(
            "'library/spells'",
            $view
        );
        self::assertStringContainsString(
            'Canonical register ready',
            $view
        );
    }

    public function testSpellbookViewUsesAccessibleNativeDisclosureAndGetSearch(): void
    {
        $view = $this->source(
            'app/Modules/Library/Views/'
            . 'spells/index.php'
        );

        self::assertStringContainsString(
            'Sage’s Spellbook',
            $view
        );
        self::assertStringContainsString(
            'Keeper of Knowledge',
            $view
        );
        self::assertStringContainsString(
            'method="get"',
            $view
        );
        self::assertStringContainsString(
            'role="search"',
            $view
        );
        self::assertStringContainsString(
            '<details',
            $view
        );
        self::assertStringContainsString(
            '<summary>',
            $view
        );
        self::assertStringContainsString(
            'aria-live="polite"',
            $view
        );
    }

    public function testSpellbookPresentationIsResponsiveAndForcedColoursSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/library/'
            . 'guild-library.css'
        );

        self::assertStringContainsString(
            '.gmrc-spellbook__grid',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-spellbook-filters',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 760px)',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 560px)',
            $css
        );
        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testSpellbookPhaseDoesNotIntegrateCharacterSpellSystemsYet(): void
    {
        $presenter = $this->source(
            'app/Modules/Library/Spells/Services/'
            . 'SpellbookPresenter.php'
        );

        self::assertStringNotContainsString(
            'ArcaneAbilityCatalogue',
            $presenter
        );
        self::assertStringNotContainsString(
            'CharacterController',
            $presenter
        );
        self::assertStringNotContainsString(
            'CharacterRepository',
            $presenter
        );
    }

    private function source(
        string $relative
    ): string {
        $source = file_get_contents(
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
