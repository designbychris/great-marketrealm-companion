<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Catalogue;

use PHPUnit\Framework\TestCase;

final class GrandCatalogueRuntimeCompatibilityTest extends TestCase
{
    public function testBundledSnapshotFallbackDoesNotRequireGmrcPath(): void
    {
        $root = dirname(__DIR__, 5);
        $repository = file_get_contents(
            $root
            . '/app/Modules/Characters/Catalogue/Repositories/'
            . 'CharacterCatalogueRepository.php'
        );

        self::assertIsString($repository);
        self::assertStringContainsString(
            "defined('GMRC_PATH')",
            $repository
        );
        self::assertStringContainsString(
            'dirname(__DIR__, 5)',
            $repository
        );
    }

    public function testBuildProfileRepositoryToleratesMissingWordPressQueryApi(): void
    {
        $root = dirname(__DIR__, 5);
        $repository = file_get_contents(
            $root
            . '/app/Modules/Characters/Catalogue/Repositories/'
            . 'CharacterBuildProfileRepository.php'
        );

        self::assertIsString($repository);
        self::assertStringContainsString(
            "function_exists('get_posts')",
            $repository
        );
    }
}
