<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits;

use PHPUnit\Framework\TestCase;

final class GreatPortraitExpansionCatalogueTest extends TestCase
{
    public function testEveryGrandCatalogueRaceHasExpandedBodyArt(): void
    {
        $root = dirname(__DIR__, 5);

        $catalogue = json_decode(
            (string) file_get_contents(
                $root . '/resources/catalogue/players-handbook.v1.json'
            ),
            true
        );

        $map = (string) file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/Services/'
            . 'PortraitRaceAssetMap.php'
        );

        self::assertIsArray($catalogue);

        foreach ($catalogue['races'] as $race) {
            self::assertStringContainsString(
                "'" . $race['key'] . "' => [",
                $map
            );

            self::assertStringContainsString(
                "'" . $race['key'] . "-body-01'",
                $map
            );

            self::assertStringContainsString(
                "'" . $race['key'] . "-body-02'",
                $map
            );
        }
    }

    public function testEveryCatalogueHeritageHasConcretePortraitAsset(): void
    {
        $root = dirname(__DIR__, 5);

        $catalogue = json_decode(
            (string) file_get_contents(
                $root . '/resources/catalogue/players-handbook.v1.json'
            ),
            true
        );

        $map = (string) file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/Services/'
            . 'PortraitRaceAssetMap.php'
        );

        self::assertIsArray($catalogue);

        foreach ($catalogue['heritages'] as $heritage) {
            $asset =
                $heritage['parent']
                . '-heritage-'
                . $heritage['key'];

            self::assertStringContainsString(
                "'" . $asset . "'",
                $map
            );
        }
    }
}
