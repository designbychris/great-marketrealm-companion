<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class LivingHeritagePreviewGuidanceTest extends TestCase
{
    public function testCharacterCreatorRendersAccessibleHeritageGuidanceRegion(): void
    {
        $source = $this->source('app/Modules/Characters/Views/create.php');

        self::assertStringContainsString('data-heritage-preview-region', $source);
        self::assertStringContainsString('aria-live="polite"', $source);
        self::assertStringContainsString('From your Heritage', $source);
        self::assertStringContainsString('Inherited from', $source);
    }

    public function testPreviewUsesStructuredHeritageAndParentMechanics(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Catalogue/Repositories/CharacterCatalogueRepository.php'
        );
        $view = $this->source('app/Modules/Characters/Views/create.php');

        self::assertStringContainsString("'parent_mechanics'", $source);
        self::assertStringContainsString("'parent_name'", $source);
        self::assertStringContainsString("'mechanics'", $view);
        self::assertStringContainsString("'ability_modifiers'", $view);
    }

    public function testCatalogueJavascriptRefreshesGuidanceOnHeritageAndRaceChanges(): void
    {
        $source = $this->source(
            'assets/js/modules/characters/grand-catalogue.js'
        );

        self::assertStringContainsString(
            'const refreshHeritagePreview = function ()',
            $source
        );
        self::assertStringContainsString(
            'preview.dataset.heritagePreview === selected',
            $source
        );
        self::assertStringContainsString(
            'refreshHeritagePreview();',
            $source
        );
    }

    public function testLegacyHeritageWithoutStructuredMechanicsHasGuidanceFallback(): void
    {
        $source = $this->source('app/Modules/Characters/Views/create.php');

        self::assertStringContainsString(
            'No additional structured bonuses are recorded',
            $source
        );
    }

    private function source(string $relative): string
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents($root . '/' . $relative);
        self::assertIsString($source);

        return $source;
    }
}
