<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Catalogue;

use PHPUnit\Framework\TestCase;

final class GrandCatalogueSnapshotTest extends TestCase
{
    public function testBundledSnapshotIsComprehensive(): void
    {
        $root = dirname(__DIR__, 5);
        $data = json_decode((string) file_get_contents($root . '/resources/catalogue/players-handbook.v1.json'), true);
        self::assertIsArray($data);
        self::assertGreaterThanOrEqual(14, count($data['races']));
        self::assertGreaterThanOrEqual(40, count($data['heritages']));
        self::assertSame(15, count($data['classes']));
        self::assertGreaterThanOrEqual(60, count($data['subclasses']));
    }

    public function testLegacyCanonicalRaceKeysRemainPresent(): void
    {
        $root = dirname(__DIR__, 5);
        $data = json_decode((string) file_get_contents($root . '/resources/catalogue/players-handbook.v1.json'), true);
        $keys = array_column($data['races'], 'key');
        self::assertContains('fructan', $keys);
        self::assertContains('vegfolk', $keys);
        self::assertContains('meatfolk', $keys);
        self::assertContains('drink-folk', $keys);
    }
}
