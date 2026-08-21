<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\HandbookSpellRegister;
use GreatMarketrealmCompanion\Modules\Library\Spells\Services\SpellbookPresenter;
use PHPUnit\Framework\TestCase;

final class SpellbookPolishCertificationRegressionTest extends TestCase
{
    public function testCertifiedRegisterStillContainsSeventyOneIdentitiesAndSeventyThreeVariants(): void
    {
        $register =
            new HandbookSpellRegister();

        self::assertCount(
            71,
            $register->all()
        );
        self::assertSame(
            73,
            $register->sourceVariantCount()
        );
    }

    public function testUnknownLevelFilterFindsOnlySourceRecordsWithoutStatedLevel(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'level' => 'unknown',
        ]);

        self::assertGreaterThan(
            0,
            $spellbook['result_count']
        );

        foreach (
            $spellbook['results']
            as $spell
        ) {
            self::assertNull(
                $spell['level']
            );
            self::assertSame(
                'Level not stated',
                $spell['level_label']
            );
        }
    }

    public function testUnknownSchoolFilterFindsOnlySourceRecordsWithoutStatedSchool(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'school' => 'unknown',
        ]);

        self::assertGreaterThan(
            0,
            $spellbook['result_count']
        );

        foreach (
            $spellbook['results']
            as $spell
        ) {
            self::assertNull(
                $spell['school']
            );
            self::assertSame(
                'School not stated',
                $spell['school_label']
            );
        }
    }

    public function testUnknownCallingAccessFilterFindsOnlyEntriesWithoutStatedAccess(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'access' => 'unknown',
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
                [],
                $spell['access_labels']
            );
        }
    }

    public function testInvalidKindFilterFallsBackToAllMagic(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'kind' =>
                'totally-not-a-valid-kind',
        ]);

        self::assertSame(
            '',
            $spellbook['filters']['kind']
        );
        self::assertSame(
            71,
            $spellbook['result_count']
        );
    }

    public function testInvalidLevelFilterFallsBackWithoutInventingLevel(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'level' => '999',
        ]);

        self::assertSame(
            '',
            $spellbook['filters']['level']
        );
        self::assertSame(
            71,
            $spellbook['result_count']
        );
    }

    public function testInvalidSchoolFilterFallsBackToAllSchools(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'school' => '<script>alert(1)</script>',
        ]);

        self::assertSame(
            '',
            $spellbook['filters']['school']
        );
        self::assertSame(
            71,
            $spellbook['result_count']
        );
    }

    public function testInvalidAccessFilterFallsBackToAllAccess(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'access' => 'Imaginary Calling',
        ]);

        self::assertSame(
            '',
            $spellbook['filters']['access']
        );
        self::assertSame(
            71,
            $spellbook['result_count']
        );
    }

    public function testSearchTextIsSanitisedBeforePresentation(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'q' =>
                '<strong>Cure Wounds</strong>',
        ]);

        self::assertSame(
            'cure wounds',
            $spellbook['filters']['q']
        );
        self::assertContains(
            'Cure Meats',
            array_column(
                $spellbook['results'],
                'name'
            )
        );
    }

    public function testSourceIssueLabelsExplainUnknownMetadataExplicitly(): void
    {
        $spellbook = (
            new SpellbookPresenter()
        )->present([
            'q' => 'Cure Meats',
        ]);

        $labels =
            $spellbook[
                'results'
            ][0]['source_issue_labels'];

        self::assertContains(
            'Level not stated in handbook',
            $labels
        );
        self::assertContains(
            'School not stated in handbook',
            $labels
        );
        self::assertContains(
            'Calling access not stated in handbook',
            $labels
        );
    }

    public function testBreadWallAndVacuumSealRemainUnreconciledSourceVariants(): void
    {
        $register =
            new HandbookSpellRegister();

        self::assertCount(
            2,
            $register
                ->find('bread-wall')
                ?->variants()
                ?? []
        );

        self::assertSame(
            [3, 4],
            array_column(
                $register
                    ->find('vacuum-seal')
                    ?->variants()
                    ?? [],
                'level'
            )
        );
    }

    public function testSourceTypoRemainsSearchableInsteadOfBeingSilentlyCorrected(): void
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

    public function testSpellbookViewExposesUnknownMetadataFiltersAndExplicitSourceNotes(): void
    {
        $view = $this->source(
            'app/Modules/Library/Views/'
            . 'spells/index.php'
        );

        self::assertStringContainsString(
            'Level not stated',
            $view
        );
        self::assertStringContainsString(
            'School not stated',
            $view
        );
        self::assertStringContainsString(
            'Calling access not stated',
            $view
        );
        self::assertStringContainsString(
            'source_issue_labels',
            $view
        );
        self::assertStringContainsString(
            'The canonical entry leaves some information',
            $view
        );
        self::assertStringContainsString(
            'unresolved:',
            $view
        );
    }

    public function testSpellbookKeyboardFocusReducedMotionAndForcedColoursRemainCertified(): void
    {
        $css = $this->source(
            'assets/css/modules/library/'
            . 'guild-library.css'
        );

        self::assertStringContainsString(
            ':focus-visible',
            $css
        );
        self::assertStringContainsString(
            'outline-offset',
            $css
        );
        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );
        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testSpellbookStillRequiresNoJavascriptForCoreBrowsing(): void
    {
        $view = $this->source(
            'app/Modules/Library/Views/'
            . 'spells/index.php'
        );

        self::assertStringContainsString(
            'method="get"',
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
        self::assertStringNotContainsString(
            'onclick=',
            $view
        );
    }

    public function testSpellbookRemainsIndependentFromCharacterSpellIntegration(): void
    {
        $presenter = $this->source(
            'app/Modules/Library/Spells/Services/'
            . 'SpellbookPresenter.php'
        );
        $controller = $this->source(
            'app/Modules/Library/Controllers/'
            . 'LibraryController.php'
        );

        foreach ([
            'ArcaneAbilityCatalogue',
            'CharacterRepository',
            'CharacterController',
            'SpellcastingProgressionCatalogue',
            'ActiveClassResourceState',
        ] as $forbidden) {
            self::assertStringNotContainsString(
                $forbidden,
                $presenter
            );
            self::assertStringNotContainsString(
                $forbidden,
                $controller
            );
        }
    }

    public function testSpellbookCertifiedCountsRemainTwentyNineRenamesAndFortyTwoOriginals(): void
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
        self::assertSame(
            71,
            $spellbook['total_count']
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
