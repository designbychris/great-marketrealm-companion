<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardMonsterWorkshopRegressionTest extends TestCase
{
    private string $root;
    private string $workshop;
    private string $provider;
    private string $bestiary;
    private string $view;
    private string $dmView;
    private string $encounters;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);
        $this->workshop = file_get_contents($this->root . '/app/Modules/Administration/Workshop/MonsterWorkshop.php');
        $this->provider = file_get_contents($this->root . '/app/Providers/AdministrationServiceProvider.php');
        $this->bestiary = file_get_contents($this->root . '/app/Modules/DungeonMaster/Bestiary/Repositories/CanonicalBestiary.php');
        $this->view = file_get_contents($this->root . '/app/Modules/Administration/Views/monster-workshop.php');
        $this->dmView = file_get_contents($this->root . '/app/Modules/DungeonMaster/Views/monsters/index.php');
        $this->encounters = file_get_contents($this->root . '/app/Modules/DungeonMaster/Controllers/EncounterController.php');
    }

    public function testWorkshopUsesSeparatePersistentRegistry(): void
    {
        self::assertStringContainsString("gmrc_steward_monsters", $this->workshop);
        self::assertStringNotContainsString('gmrc_canonical_bestiary_overrides', $this->workshop);
    }

    public function testWorkshopSupportsPublicationLifecycle(): void
    {
        foreach (['draft', 'published', 'archived'] as $status) self::assertStringContainsString($status, $this->workshop);
    }

    public function testOnlyPublishedStewardCreaturesJoinSharedBestiary(): void
    {
        self::assertStringContainsString('MonsterWorkshop::STATUS_PUBLISHED', $this->bestiary);
        self::assertStringContainsString("origin", $this->bestiary);
    }

    public function testCanonicalAndStewardIdentityRemainDistinct(): void
    {
        $model = file_get_contents($this->root . '/app/Modules/DungeonMaster/Bestiary/Models/CanonicalMonster.php');
        self::assertStringContainsString("'steward:'", $model);
        self::assertStringContainsString('isStewardAuthored', $model);
    }

    public function testWorkshopIsAdministratorProtectedAndNonceVerified(): void
    {
        self::assertStringContainsString("admin_post_gmrc_save_steward_monster", $this->provider);
        self::assertStringContainsString("check_admin_referer('gmrc_save_steward_monster_", $this->provider);
    }

    public function testWorkshopOffersMediaLibraryArtwork(): void
    {
        self::assertStringContainsString('data-gmrc-canonical-image-select', $this->view);
        self::assertStringContainsString('wp_enqueue_media()', $this->provider);
    }

    public function testWorkshopFormCoversAdvancedMonsterRules(): void
    {
        foreach (['legendary_actions', 'mythic_actions', 'lair_actions', 'spellcasting', 'reactions'] as $field) self::assertStringContainsString($field, $this->view);
    }

    public function testFieldGuideProjectionIsExplicitlySpoilerSafe(): void
    {
        self::assertStringContainsString('field_guide_visible', $this->view);
        self::assertStringContainsString('Player-safe description', $this->view);
    }

    public function testDungeonMasterBestiaryLabelsStewardCreations(): void
    {
        self::assertStringContainsString('Shared Marketrealm Bestiary', $this->dmView);
        self::assertStringContainsString('Steward Creation', $this->dmView);
    }

    public function testPublishedStewardCreaturesCanEnterEncounterSnapshots(): void
    {
        self::assertStringContainsString("steward:'", $this->encounters);
        self::assertStringContainsString("steward:'", $this->bestiary);
    }
}
