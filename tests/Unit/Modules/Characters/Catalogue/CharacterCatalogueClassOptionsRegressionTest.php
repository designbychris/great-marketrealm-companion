<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Catalogue;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterCatalogueRepository;
use PHPUnit\Framework\TestCase;

final class CharacterCatalogueClassOptionsRegressionTest extends TestCase
{
    public function testLegacySpecialtiesAreNotTopLevelCallingOptions(): void
    {
        $options = (new CharacterCatalogueRepository())->classOptions();

        self::assertArrayNotHasKey('grocer', $options);
        self::assertArrayNotHasKey('cleaver-saint', $options);
        self::assertArrayHasKey('fighter', $options);
        self::assertArrayHasKey('monk', $options);
        self::assertCount(13, $options);
    }

    public function testCatalogueVersionInvalidatesOlderStoredSnapshot(): void
    {
        $repository = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Catalogue/Repositories/'
            . 'CharacterCatalogueRepository.php'
        );

        self::assertIsString($repository);
        self::assertStringContainsString(
            "private const VERSION = '3.7.2';",
            $repository
        );

        $catalogue = json_decode(
            (string) file_get_contents(
                $this->root()
                . '/resources/catalogue/players-handbook.v1.json'
            ),
            true
        );

        self::assertIsArray($catalogue);
        self::assertSame('3.7.2', $catalogue['version']);
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}
