<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class TabletopBestiaryBridgeRegressionTest extends TestCase
{
    private function root(string $path): string { return dirname(__DIR__, 4) . '/' . ltrim($path, '/'); }

    public function test_dungeon_master_provider_publishes_neutral_tabletop_bestiary_filter(): void
    {
        $provider=file_get_contents($this->root('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php'));
        self::assertStringContainsString('gmrc_tabletop_bestiary_records', $provider);
        self::assertStringContainsString('tabletopBestiaryRecords', $provider);
    }

    public function test_canonical_monster_projects_an_encounter_ready_neutral_record(): void
    {
        $monster=file_get_contents($this->root('app/Modules/DungeonMaster/Bestiary/Models/CanonicalMonster.php'));
        self::assertStringContainsString('tabletopBestiaryRecord', $monster);
        self::assertStringContainsString("'armor_class' => \$this->armorClass()", $monster);
        self::assertStringContainsString("'hit_points' => \$this->maxHp()", $monster);
        self::assertStringContainsString("'attacks' => \$this->tabletopAttacks()", $monster);
        self::assertStringContainsString("'source' => 'gmrc-bestiary:'", $monster);
    }

    public function test_bridge_does_not_import_tabletop_classes(): void
    {
        foreach (['app/Modules/DungeonMaster/DungeonMasterServiceProvider.php','app/Modules/DungeonMaster/Bestiary/Models/CanonicalMonster.php'] as $file) {
            self::assertStringNotContainsString('GreatMarketrealmTabletop\\', file_get_contents($this->root($file)));
        }
    }
}
