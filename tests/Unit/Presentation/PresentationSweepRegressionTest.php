<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

final class PresentationSweepRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 3);
    }

    public function testRenderedViewsDoNotExposeDevelopmentPhaseReferences(): void
    {
        foreach ($this->viewFiles() as $file) {
            $source = file_get_contents($file);
            self::assertIsString($source);
            self::assertDoesNotMatchRegularExpression(
                '/(?:Phase\s+)?III\.\d+(?:\.\d+)+(?:[A-Z])?/i',
                $source,
                'Development phase reference leaked into rendered view: ' . $file
            );
        }
    }

    public function testAdvancementAndGuildLibraryDoNotRenderPhaseMetadata(): void
    {
        $advancement = $this->source('app/Modules/Characters/Views/advancement.php');
        $library = $this->source('app/Modules/Library/Views/index.php');

        self::assertStringNotContainsString("\$delegated['phase']", $advancement);
        self::assertStringNotContainsString('Assigned to Phase', $advancement);
        self::assertStringNotContainsString("\$domain['phase']", $library);
        self::assertStringNotContainsString('gmrc-guild-library-card__phase', $library);
    }

    public function testPresentationCopyUsesWorldFacingLabelsInstead(): void
    {
        self::assertStringContainsString(
            'Dungeon Master Guide canon',
            $this->source('app/Modules/DungeonMaster/Views/monsters/index.php')
        );
        self::assertStringContainsString(
            'Private DM Chronicle',
            $this->source('app/Modules/DungeonMaster/Views/journal/index.php')
        );
        self::assertStringContainsString(
            'The Rising Folios',
            $this->source('app/Modules/Characters/Views/advancement.php')
        );
        self::assertStringContainsString(
            'Restricted Archive',
            $this->source('app/Modules/Library/Views/relics/index.php')
        );
    }

    public function testInternalPhaseMetadataRemainsAvailableForDevelopment(): void
    {
        $progression = $this->source(
            'app/Modules/Characters/Progression/Definitions/Classes/BarbarianProgression.php'
        );
        $catalogue = $this->source(
            'app/Modules/Library/Catalogues/BackgroundReferenceCatalogue.php'
        );

        self::assertStringContainsString("'phase' => 'III.12.3'", $progression);
        self::assertStringContainsString("return 'III.13.3'", $catalogue);
    }

    /** @return string[] */
    private function viewFiles(): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->root . '/app')
        );
        $files = new RegexIterator($iterator, '/\/Views\/.*\.php$/i');

        return array_values(array_map(
            static fn($file): string => $file->getPathname(),
            iterator_to_array($files, false)
        ));
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
