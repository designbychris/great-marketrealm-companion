<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use PHPUnit\Framework\TestCase;

final class FieldGuidePublicationPassRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testPublicationRegisterSeedsPlayerSafeCanonicalLore(): void
    {
        $source = $this->source(
            'app/Modules/DungeonMaster/Bestiary/Data/guild-field-guide-publications.php'
        );

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
        $source = $this->source(
            'app/Modules/DungeonMaster/Bestiary/Repositories/CanonicalBestiary.php'
        );

        self::assertStringContainsString('guild-field-guide-publications.php', $source);
        self::assertStringContainsString("'field_guide_visible' => true", $source);
        self::assertStringContainsString("'player_description' => (string) \$publications[\$key]", $source);
        self::assertMatchesRegularExpression(
            '/array_merge\\(\\s*\\$entry,\\s*\\$publication,\\s*\\$override,/s',
            $source
        );
    }

    public function testPublicationPassDoesNotAutoPublishEveryBestiaryRecord(): void
    {
        $publication = $this->source(
            'app/Modules/DungeonMaster/Bestiary/Data/guild-field-guide-publications.php'
        );
        $bestiary = $this->source(
            'app/Modules/DungeonMaster/Bestiary/Data/dungeon-master-guide-monsters.php'
        );

        self::assertStringContainsString("'pickled-basilisk'", $publication);
        self::assertStringContainsString("'tim-cursed-recipe-book'", $bestiary);
        self::assertStringNotContainsString("'tim-cursed-recipe-book'", $publication);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);

        return $source;
    }
}
