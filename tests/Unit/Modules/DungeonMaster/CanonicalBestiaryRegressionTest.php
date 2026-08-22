<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class CanonicalBestiaryRegressionTest extends TestCase
{
    public function testCanonicalRegisterPreservesGuideCreaturesAndSourceBoundary(): void
    {
        $data = $this->source('app/Modules/DungeonMaster/Bestiary/Data/dungeon-master-guide-monsters.php');
        foreach (['Pickled Basilisk', 'Croissant Dragon', 'Gor’Garnash, The Devourer Prime', 'Meat Obilisk', 'The Cornucopia', 'Rotling', 'Tim, the Cursed Recipe Book'] as $name) {
            self::assertStringContainsString($name, $data);
        }
        self::assertStringContainsString('does not state ability scores', $data);
        self::assertStringContainsString('second Spoiled Shambler stat block', $data);
    }

    public function testCanonicalCreaturesAreReadOnlyAndDoNotUseMonsterPosts(): void
    {
        $record = $this->source('app/Modules/DungeonMaster/Bestiary/Models/CanonicalMonster.php');
        $register = $this->source('app/Modules/DungeonMaster/Bestiary/Repositories/CanonicalBestiary.php');
        self::assertStringContainsString("'canonical:'", $record);
        self::assertStringContainsString('encounterReady', $record);
        self::assertStringNotContainsString('wp_insert_post', $register);
        self::assertStringNotContainsString('update_post_meta', $register);
    }

    public function testBestiaryAndEncounterBoardExposeCanonicalShelf(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/MonsterController.php');
        $encounters = $this->source('app/Modules/DungeonMaster/Controllers/EncounterController.php');
        $view = $this->source('app/Modules/DungeonMaster/Views/monsters/index.php');
        self::assertStringContainsString("'canonicalMonsters'", $controller);
        self::assertStringContainsString("str_starts_with(\$monsterId, 'canonical:')", $encounters);
        self::assertStringContainsString('Canonical Marketrealm Bestiary', $view);
        self::assertStringContainsString('Reference only', $view);
    }

    private function source(string $relative): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }
}
