<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Catalogue;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Services\SubclassPreviewCatalogue;
use PHPUnit\Framework\TestCase;

final class SubclassPreviewRegressionTest extends TestCase
{
    public function testPreviewCatalogueIncludesEveryRegisteredSubclass(): void
    {
        $previews = (
            new SubclassPreviewCatalogue()
        )->all();

        self::assertArrayHasKey(
            'oath-of-inventory',
            $previews
        );

        self::assertArrayHasKey(
            'way-of-the-spun-cloud',
            $previews
        );

        self::assertArrayHasKey(
            'path-of-the-butchered-rage',
            $previews
        );
    }

    public function testPaladinOathsAlreadyExposeUsefulChoiceGuidance(): void
    {
        $previews = (
            new SubclassPreviewCatalogue()
        )->all();

        foreach (
            [
                'oath-of-inventory',
                'oath-of-the-colonel',
                'oath-of-the-creamfather',
                'oath-of-aroma',
                'oath-of-clearance',
                'oath-of-seasoning',
                'oath-of-carbonation',
                'oath-of-the-cleaver-saint',
            ]
            as $oath
        ) {
            self::assertNotSame(
                '',
                $previews[$oath]['identity']
            );

            self::assertNotSame(
                '',
                $previews[$oath]['playstyle']
            );

            self::assertNotSame(
                '',
                $previews[$oath]['best_for']
            );

            self::assertSame(
                'paladin',
                $previews[$oath]['parent']
            );
        }
    }

    public function testCertifiedPathGiftsFlowIntoCreationPreviewAutomatically(): void
    {
        $previews = (
            new SubclassPreviewCatalogue()
        )->all();

        $monk = $previews[
            'way-of-the-spun-cloud'
        ];

        self::assertSame(
            [3, 6, 11, 17],
            array_column(
                $monk['gift_preview'],
                'level'
            )
        );

        self::assertSame(
            'Sugarwind Step',
            $monk['gift_preview'][0]['label']
        );
    }

    public function testCertifiedPaladinOathNowShowsGiftPreview(): void
    {
        $preview = (
            new SubclassPreviewCatalogue()
        )->all()['oath-of-inventory'];

        self::assertSame(
            [3, 7, 15, 20],
            array_column(
                $preview['gift_preview'],
                'level'
            )
        );

        self::assertSame(
            'Sacred Stocktake',
            $preview['gift_preview'][0]['label']
        );
    }

    public function testCharacterCreationReceivesSharedSubclassPreviewCatalogue(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'SubclassPreviewCatalogue',
            $controller
        );

        self::assertStringContainsString(
            "'subclassPreviews'",
            $controller
        );
    }

    public function testCreationViewRendersAccessiblePreviewRegion(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/create.php'
        );

        self::assertStringContainsString(
            'data-subclass-preview-region',
            $view
        );

        self::assertStringContainsString(
            'data-subclass-preview=',
            $view
        );

        self::assertStringContainsString(
            'aria-live="polite"',
            $view
        );

        self::assertStringContainsString(
            'How it plays',
            $view
        );

        self::assertStringContainsString(
            'Best for',
            $view
        );

        self::assertStringContainsString(
            'Future gifts',
            $view
        );
    }

    public function testCatalogueJavascriptUpdatesPreviewOnSubclassAndClassChange(): void
    {
        $script = $this->source(
            'assets/js/modules/characters/'
            . 'grand-catalogue.js'
        );

        self::assertStringContainsString(
            'refreshSubclassPreview',
            $script
        );

        self::assertStringContainsString(
            '[data-subclass-preview]',
            $script
        );

        self::assertStringContainsString(
            'target === subclass',
            $script
        );

        self::assertStringContainsString(
            'preview.hidden = !visible',
            $script
        );
    }

    public function testPreviewPresentationIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'grand-catalogue.css'
        );

        self::assertStringContainsString(
            '.gmrc-subclass-preview',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 720px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
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
        return dirname(__DIR__, 5);
    }
}
