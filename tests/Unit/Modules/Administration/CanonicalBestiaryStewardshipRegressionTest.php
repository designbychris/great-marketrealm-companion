<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class CanonicalBestiaryStewardshipRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testStewardProviderRegistersCanonicalBestiaryActionsAndMedia(): void
    {
        $provider = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('CanonicalBestiarySteward::class', $provider);
        self::assertStringContainsString('admin_post_gmrc_save_canonical_monster', $provider);
        self::assertStringContainsString('admin_post_gmrc_reset_canonical_monster', $provider);
        self::assertStringContainsString('wp_enqueue_media()', $provider);
        self::assertStringContainsString('canonical-bestiary.js', $provider);
        self::assertStringContainsString("current_user_can(self::CAPABILITY)", $provider);
    }

    public function testCanonicalOverridesUseDedicatedOptionAndImageValidation(): void
    {
        $service = $this->source('app/Modules/Administration/CanonicalRecords/CanonicalBestiarySteward.php');
        self::assertStringContainsString("OPTION = 'gmrc_canonical_bestiary_overrides'", $service);
        self::assertStringContainsString('wp_attachment_is_image', $service);
        self::assertStringContainsString('update_option(self::OPTION', $service);
        self::assertStringContainsString("'image_attachment_id'", $service);
        self::assertStringContainsString('$this->bestiary->flush()', $service);
    }

    public function testCanonicalBestiaryMergesOverridesWithoutMonsterPosts(): void
    {
        $repository = $this->source('app/Modules/DungeonMaster/Bestiary/Repositories/CanonicalBestiary.php');
        self::assertStringContainsString('get_option(CanonicalBestiarySteward::OPTION', $repository);
        self::assertStringContainsString('array_merge($entry, $override', $repository);
        self::assertStringContainsString('public function flush()', $repository);
        self::assertStringNotContainsString('wp_insert_post', $repository);
        self::assertStringNotContainsString('wp_update_post', $repository);
    }

    public function testStewardViewSupportsStatsTraitsActionsAndMediaLibraryArtwork(): void
    {
        $view = $this->source('app/Modules/Administration/Views/canonical-records.php');
        foreach (['Armor Class', 'Hit Points', 'Ability Scores', 'Special Traits', 'Actions', 'Bestiary Artwork'] as $label) {
            self::assertStringContainsString($label, $view);
        }
        self::assertStringContainsString('image_attachment_id', $view);
        self::assertStringContainsString('Choose / Replace Image', $view);
        self::assertStringContainsString('Restore Dungeon Master Guide baseline', $view);
        self::assertStringContainsString('gmrc_save_canonical_monster_', $view);
    }

    public function testBestiaryListingLinksCanonicalCardsAndShowsArtwork(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/monsters/index.php');
        self::assertStringContainsString("'dungeon-master/monsters/canonical/' . \$monster->key()", $view);
        self::assertStringContainsString('imageAttachmentId()', $view);
        self::assertStringContainsString('gmrc-canonical-bestiary__image', $view);
        self::assertStringContainsString('Bestiary folio', $view);
    }

    public function testCanonicalCreatureHasInteractiveFolioWithFullRulesSections(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/MonsterController.php');
        $view = $this->source('app/Modules/DungeonMaster/Views/monsters/canonical.php');
        self::assertStringContainsString("'/dungeon-master/monsters/canonical/{monsterKey}'", $routes);
        self::assertStringContainsString('showCanonical', $routes);
        self::assertStringContainsString('showCanonical(string $monsterKey)', $controller);
        self::assertStringContainsString("str_starts_with(\$monsterId, 'canonical:')", $controller);
        self::assertStringContainsString('dungeonmaster.monsters.canonical', $controller);
        self::assertStringContainsString('Canonical Marketrealm Bestiary · Field Folio', $view);
        self::assertStringContainsString('Special Traits', $view);
        self::assertStringContainsString('Actions', $view);
        self::assertStringContainsString('Ability scores', $view);
        self::assertStringContainsString('imageAttachmentId()', $view);
    }

    public function testArtworkUsesTornPaperTreatmentAndAccessibleFallbacks(): void
    {
        $adminCss = $this->source('assets/css/admin.css');
        $monsterCss = $this->source('assets/css/modules/dungeon-master/monster-ledger.css');
        self::assertStringContainsString('gmrc-canonical-steward__paper-frame', $adminCss);
        self::assertStringContainsString('clip-path:polygon', $this->compact($adminCss));
        self::assertStringContainsString('forced-colors:active', $adminCss);
        self::assertStringContainsString('gmrc-canonical-folio__portrait', $monsterCss);
        self::assertStringContainsString('gmrc-canonical-bestiary__image', $monsterCss);
        self::assertStringContainsString('forced-colors:active', $monsterCss);
    }

    public function testDocumentationRecordsBestiaryStewardshipCheckpoint(): void
    {
        $docs = $this->source('docs/Phase-III.16.4-Canonical-Bestiary-Stewardship.md');
        self::assertStringContainsString('Phase III.16.4 — Canonical Records / Bestiary Stewardship', $docs);
        self::assertStringContainsString('3,513 tests', $docs);
        self::assertStringContainsString('11,872 assertions', $docs);
        self::assertStringContainsString('Encounter snapshots', $docs);
        self::assertStringContainsString('WordPress Media Library', $docs);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }

    private function compact(string $source): string
    {
        return (string) preg_replace('/\s+/', '', $source);
    }
}
