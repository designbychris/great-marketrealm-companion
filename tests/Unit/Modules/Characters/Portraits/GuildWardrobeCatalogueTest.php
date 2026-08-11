<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits;

use PHPUnit\Framework\TestCase;

final class GuildWardrobeCatalogueTest extends TestCase
{
    public function testEveryHandbookClassHasWardrobeAssets(): void
    {
        $root = dirname(__DIR__, 5);

        $catalogue = json_decode(
            (string) file_get_contents(
                $root
                . '/resources/catalogue/'
                . 'players-handbook.v1.json'
            ),
            true
        );

        $map = file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/Services/'
            . 'PortraitClassAssetMap.php'
        );

        self::assertIsArray($catalogue);
        self::assertIsString($map);

        foreach ($catalogue['classes'] as $class) {
            $key = $class['key'];

            self::assertStringContainsString(
                "'" . $key . "' => [",
                $map
            );

            foreach ([
                '-outfit-01',
                '-outfit-02',
                '-equipment-01',
                '-equipment-02',
                '-accessory-01',
                '-effects-01',
                '-ornament-01',
            ] as $suffix) {
                self::assertStringContainsString(
                    "'" . $key . $suffix . "'",
                    $map
                );
            }
        }
    }

    public function testWardrobeMapKeepsRaceAndClassIndependent(): void
    {
        $root = dirname(__DIR__, 5);

        $map = file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/Services/'
            . 'PortraitClassAssetMap.php'
        );

        self::assertIsString($map);

        self::assertStringNotContainsString(
            'PortraitRaceAssetMap',
            $map
        );

        self::assertStringContainsString(
            'forClass(',
            $map
        );
    }
}
