<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use PHPUnit\Framework\TestCase;

final class FieldGuidePublicationPassRegressionTest extends TestCase
{
    public function testPublicationRegisterSeedsPlayerSafeCanonicalLore(): void
    {
        $source = file_get_contents($this->root() . '/app/Modules/DungeonMaster/Bestiary/Data/guild-field-guide-publications.php');
        self::assertIsString($source);
        self::assertStringContainsString("'pickled-basilisk'", $source);
        self::assertStringContainsString("'croissant-dragon'", $source);
        self::assertStringContainsString("'rollback-wyrm'", $source);
        self::assertStringNotContainsString("'ac' =>", $source);
        self::assertStringNotContainsString("'hp' =>", $source);
        self::assertStringNotContainsString("'traits' =>", $source);
        self::assertStringNotContainsString("'actions' =>", $source);
    }

    public function testCanonicalBestiaryAppliesPublicationBeforeStewardOverride(): void
    {
        $source = file_get_contents($this->root() . '/app/Modules/DungeonMaster/Bestiary/Repositories/CanonicalBestiary.php');
        self::assertIsString($source);
        self::assertStringContainsString('guild-field-guide-publications.php', $source);
        self::assertStringContainsString("'field_guide_visible' => true", $source);
        self::assertStringContainsString("'player_description' => (string) \$publications[\$key]", $source);
        self::assertMatchesRegularExpression('/array_merge\\(\\s*\\$entry,\\s*\\$publication,\\s*\\$override,/s', $source);
    }

    public function testPublicationPassDoesNotAutoPublishEveryBestiaryRecord(): void
    {
        $publication = file_get_contents($this->root() . '/app/Modules/DungeonMaster/Bestiary/Data/guild-field-guide-publications.php');
        $bestiary = file_get_contents($this->root() . '/app/Modules/DungeonMaster/Bestiary/Data/dungeon-master-guide-monsters.php');
        self::assertIsString($publication);
        self::assertIsString($bestiary);
        self::assertStringContainsString("'pickled-basilisk'", $publication);
        self::assertStringContainsString("'tim-cursed-recipe-book'", $bestiary);
        self::assertStringNotContainsString("'tim-cursed-recipe-book'", $publication);
    }
}
