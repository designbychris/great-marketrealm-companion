<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class EncounterBoardRegressionTest extends TestCase
{
    private string $root;
    protected function setUp(): void { parent::setUp(); $this->root = dirname(__DIR__, 4); }

    public function testEncounterRoutesAreNestedUnderOwnedCampaign(): void
    {
        $routes=$this->source('app/Modules/DungeonMaster/Routes.php'); $compact=preg_replace('/\s+/','',$routes);
        self::assertStringContainsString("'/dungeon-master/campaigns/{id}/encounters'",$compact);
        self::assertStringContainsString("'/dungeon-master/campaigns/{id}/encounters/create'",$compact);
        self::assertStringContainsString("'/dungeon-master/campaigns/{id}/encounters/{encounterId}'",$compact);
        self::assertStringContainsString('EncounterController::class',$routes);
    }

    public function testEncountersUsePrivateCampaignOwnedWordPressPersistence(): void
    {
        $repo=$this->source('app/Modules/DungeonMaster/Repositories/EncounterRepository.php'); $provider=$this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');
        self::assertStringContainsString("POST_TYPE = 'gmrc_encounter'",$repo);
        self::assertStringContainsString('_gmrc_encounter_campaign_id',$repo);
        self::assertStringContainsString("'post_author' => \$campaign->ownerId()",$repo);
        self::assertStringContainsString("'post_parent' => \$campaignPostId",$repo);
        self::assertStringContainsString('EncounterRepository::POST_TYPE',$provider);
        self::assertStringContainsString("'public'=>false",str_replace(' ','',$provider));
    }

    public function testEncounterLifecycleAndPlanningFieldsAreFirstClass(): void
    {
        $model=$this->source('app/Modules/DungeonMaster/Models/Encounter.php'); $repo=$this->source('app/Modules/DungeonMaster/Repositories/EncounterRepository.php');
        self::assertStringContainsString('Ulid::generate()',$model);
        self::assertStringContainsString('STATUS_PREPARED',$model); self::assertStringContainsString('STATUS_RUNNING',$model); self::assertStringContainsString('STATUS_COMPLETED',$model);
        self::assertStringContainsString('THREAT_DEADLY',$model);
        self::assertStringContainsString('_gmrc_encounter_session_id',$repo); self::assertStringContainsString('_gmrc_encounter_adversaries',$repo); self::assertStringContainsString('_gmrc_encounter_character_ids',$repo);
    }

    public function testSessionAssignmentAndCharactersAreCampaignScoped(): void
    {
        $controller=$this->source('app/Modules/DungeonMaster/Controllers/EncounterController.php');
        self::assertStringContainsString('$this->sessions->findForCampaign($sessionId, $campaign)',$controller);
        self::assertStringContainsString('$this->rosters->members($campaign)',$controller);
        self::assertStringContainsString('array_intersect($submitted, array_unique($allowed))',$controller);
        self::assertStringContainsString('findForOwner(CharacterId::fromString',$controller);
    }

    public function testEncounterMutationsUseDmCapabilityAndCampaignNonce(): void
    {
        $request=$this->source('app/Modules/DungeonMaster/Requests/SaveEncounterRequest.php'); $frontend=$this->source('app/Providers/FrontendServiceProvider.php'); $form=$this->source('app/Modules/DungeonMaster/Views/encounters/_form.php');
        self::assertStringContainsString("current_user_can('gmrc_manage_campaigns')",$request);
        self::assertStringContainsString('gmrc_dm_encounter_',$frontend); self::assertStringContainsString('gmrc_dm_encounter_',$form);
        self::assertStringContainsString("['POST', 'PUT']",$frontend);
    }

    public function testArchivedCampaignEncounterBoardIsReadOnly(): void
    {
        $controller=$this->source('app/Modules/DungeonMaster/Controllers/EncounterController.php'); $index=$this->source('app/Modules/DungeonMaster/Views/encounters/index.php'); $show=$this->source('app/Modules/DungeonMaster/Views/encounters/show.php');
        self::assertStringContainsString('Archived campaigns have a read-only Encounter Board.',$controller);
        self::assertStringContainsString('preserved as read-only history',$index);
        self::assertStringContainsString("if(!\$campaign->isArchived())",str_replace(' ','',$show));
    }

    public function testCampaignAndDeskOpenEncounterBoard(): void
    {
        $campaign=$this->source('app/Modules/DungeonMaster/Views/campaigns/show.php'); $desk=$this->source('app/Modules/DungeonMaster/Views/index.php');
        self::assertStringContainsString('Open Encounter Board',$campaign); self::assertStringContainsString('/encounters',$campaign);
        self::assertStringContainsString('Ledger III · Open',$desk); self::assertStringContainsString('Choose Campaign',$desk);
    }

    public function testEncounterBoardUsesSafeDmWorkspaceAndAccessibilityFallbacks(): void
    {
        $css=$this->source('assets/css/modules/dungeon-master/encounter-board.css'); $frontend=$this->source('app/Providers/FrontendServiceProvider.php'); $compact=preg_replace('/\s+/','',$css);
        self::assertStringContainsString('.gmrc-content:has(>.gmrc-encounter-board)',$compact); self::assertStringNotContainsString('.gmrc-app-main:has(',$css);
        self::assertStringContainsString('dungeon-master-desk-background.png',$css); self::assertStringContainsString(':focus-visible',$css);
        self::assertStringContainsString('forced-colors:active',$compact); self::assertStringContainsString('prefers-reduced-transparency:reduce',$compact); self::assertStringContainsString('background-attachment:scroll',$compact);
        self::assertStringContainsString('gmrc-encounter-board',$frontend); self::assertStringContainsString('dungeon-master/encounter-board.css',$frontend);
    }

    public function testPhaseDocumentationRecordsEncounterBoardCheckpoint(): void
    {
        $docs=$this->source('docs/GuildArchives/Development/DungeonMasterPhase315.md');
        self::assertStringContainsString('III.15.4 — The Encounter Board',$docs); self::assertStringContainsString('3,426 tests',$docs); self::assertStringContainsString('11,364 assertions',$docs);
        self::assertStringContainsString('Phase III.15.5',$docs);
    }

    private function source(string $path): string { $source=file_get_contents($this->root.'/'.$path); self::assertIsString($source); return $source; }
}
