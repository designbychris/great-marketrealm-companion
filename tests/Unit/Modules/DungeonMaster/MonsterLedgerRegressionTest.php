<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class MonsterLedgerRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testMonsterLedgerRoutesAreFirstClassDmRoutes(): void
    {
        $source = $this->compact($this->source('app/Modules/DungeonMaster/Routes.php'));
        self::assertStringContainsString("'/dungeon-master/monsters'", $source);
        self::assertStringContainsString("'/dungeon-master/monsters/create'", $source);
        self::assertStringContainsString("'/dungeon-master/monsters/{monsterId}'", $source);
        self::assertStringContainsString("'/dungeon-master/monsters/{monsterId}/archive'", $source);
        self::assertStringContainsString('MonsterController::class', $source);
    }

    public function testMonsterLedgerUsesPrivateOwnerScopedWordPressPersistence(): void
    {
        $repo = $this->source('app/Modules/DungeonMaster/Repositories/MonsterRepository.php');
        $provider = $this->compact($this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php'));
        self::assertStringContainsString("POST_TYPE = 'gmrc_monster'", $repo);
        self::assertStringContainsString("'author' => \$ownerId", $repo);
        self::assertStringContainsString("'post_author' => \$monster->ownerId()", $repo);
        self::assertStringContainsString('MonsterRepository::POST_TYPE', $provider);
        self::assertStringContainsString("'public'=>false", $provider);
        self::assertStringContainsString("'show_ui'=>false", $provider);
    }

    public function testMonsterStatBlockAndInitiativeModifierAreFirstClass(): void
    {
        $model = $this->source('app/Modules/DungeonMaster/Models/Monster.php');
        self::assertStringContainsString('Ulid::generate()', $model);
        foreach (['armorClass', 'maxHp', 'strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma', 'traits', 'actions'] as $field) {
            self::assertStringContainsString($field, $model);
        }
        self::assertStringContainsString('initiativeModifier()', $model);
        self::assertStringContainsString('floor(($this->dexterity - 10) / 2)', $model);
        self::assertStringContainsString('encounterSnapshot', $model);
    }

    public function testMonsterMutationsUseDmCapabilityAndDedicatedNonces(): void
    {
        $request = $this->source('app/Modules/DungeonMaster/Requests/SaveMonsterRequest.php');
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $form = $this->source('app/Modules/DungeonMaster/Views/monsters/_form.php');
        $show = $this->source('app/Modules/DungeonMaster/Views/monsters/show.php');
        self::assertStringContainsString("current_user_can('gmrc_manage_campaigns')", $request);
        self::assertStringContainsString('gmrc_dm_monster_create', $frontend);
        self::assertStringContainsString('gmrc_dm_monster_', $frontend);
        self::assertStringContainsString('gmrc_dm_monster_', $form);
        self::assertStringContainsString('gmrc_dm_monster_', $show);
    }

    public function testEncounterStoresOnlyOwnedMonsterSnapshots(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/EncounterController.php');
        $model = $this->source('app/Modules/DungeonMaster/Models/Encounter.php');
        $repo = $this->source('app/Modules/DungeonMaster/Repositories/EncounterRepository.php');
        self::assertStringContainsString('$this->monsters->findForOwner($monsterId, $campaign->ownerId())', $controller);
        self::assertStringContainsString('$monster->encounterSnapshot($quantity)', $controller);
        self::assertStringContainsString('monsterGroups()', $model);
        self::assertStringContainsString('_gmrc_encounter_monster_groups', $repo);
    }

    public function testEncounterFormIntegratesBestiaryWithoutRemovingLooseAdversaries(): void
    {
        $form = $this->source('app/Modules/DungeonMaster/Views/encounters/_form.php');
        $request = $this->source('app/Modules/DungeonMaster/Requests/SaveEncounterRequest.php');
        self::assertStringContainsString('Monster Ledger adversaries', $form);
        self::assertStringContainsString('monster_quantities[', $form);
        self::assertStringContainsString('Loose adversaries / hazards', $form);
        self::assertStringContainsString("'monster_quantities' => ['array']", $request);
        self::assertStringContainsString('monsterQuantities()', $request);
    }

    public function testInitiativeSeedsStructuredMonstersWithSnapshotHpAndModifier(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/InitiativeController.php');
        self::assertStringContainsString('$encounter->monsterGroups()', $controller);
        self::assertStringContainsString("['max_hp']", $controller);
        self::assertStringContainsString("['initiative_modifier']", $controller);
        self::assertStringContainsString("'source_id'=>$monsterId", $this->compact($controller));
        self::assertStringContainsString('$encounter->adversaries()', $controller);
        self::assertStringContainsString('$encounter->monsterGroups()', $controller);
    }

    public function testArchivingCreatureIsNonDestructive(): void
    {
        $model = $this->source('app/Modules/DungeonMaster/Models/Monster.php');
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/MonsterController.php');
        self::assertStringContainsString("STATUS_ARCHIVED = 'archived'", $model);
        self::assertStringContainsString('$monster->archive()', $controller);
        self::assertStringContainsString('without disturbing existing Encounter snapshots', $controller);
        self::assertStringNotContainsString('wp_delete_post', $controller);
    }

    public function testDmDeskAndAssetsOpenMonsterLedger(): void
    {
        $desk = $this->source('app/Modules/DungeonMaster/Views/index.php');
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        self::assertStringContainsString('Ledger V · Open', $desk);
        self::assertStringContainsString('Monster Ledger', $desk);
        self::assertStringContainsString('Open Bestiary', $desk);
        self::assertStringContainsString('gmrc-monster-ledger', $frontend);
        self::assertStringContainsString('dungeon-master/monster-ledger.css', $frontend);
    }

    public function testMonsterLedgerUsesSafeDmWorkspaceAndAccessibilityFallbacks(): void
    {
        $css = $this->source('assets/css/modules/dungeon-master/monster-ledger.css');
        $compact = $this->compact($css);
        self::assertStringContainsString('.gmrc-content:has(>.gmrc-monster-ledger)', $compact);
        self::assertStringNotContainsString('.gmrc-app-main:has(', $css);
        self::assertStringContainsString('dungeon-master-desk-background.png', $css);
        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('prefers-reduced-transparency:reduce', $compact);
        self::assertStringContainsString('forced-colors:active', $compact);
        self::assertStringContainsString('background-attachment:scroll', $compact);
    }

    public function testDocumentationRecordsMonsterLedgerCheckpoint(): void
    {
        $docs = $this->source('docs/GuildArchives/Development/DungeonMasterPhase315.md');
        self::assertStringContainsString('III.15.6 — The Monster Ledger / Bestiary Integration', $docs);
        self::assertStringContainsString('3,444 tests', $docs);
        self::assertStringContainsString('11,471 assertions', $docs);
        self::assertStringContainsString('Phase III.15.7', $docs);
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
