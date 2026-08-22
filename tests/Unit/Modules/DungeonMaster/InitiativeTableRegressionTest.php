<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class InitiativeTableRegressionTest extends TestCase
{
    private string $root;
    protected function setUp(): void { parent::setUp(); $this->root=dirname(__DIR__,4); }
    public function testInitiativeRoutesAreNestedUnderEncounter(): void { $s=$this->source('app/Modules/DungeonMaster/Routes.php');$c=preg_replace('/\s+/','',$s);self::assertStringContainsString("'/dungeon-master/campaigns/{id}/encounters/{encounterId}/initiative'",$c);self::assertStringContainsString('InitiativeController::class',$s); }
    public function testInitiativeStatePersistsOnOwnedEncounter(): void { $s=$this->source('app/Modules/DungeonMaster/Repositories/EncounterRepository.php');self::assertStringContainsString('_gmrc_encounter_initiative_table',$s);self::assertStringContainsString('initiativeForCampaign',$s);self::assertStringContainsString('saveInitiative',$s);self::assertStringContainsString('$this->findPost($encounterId, $campaign)',$s); }
    public function testCombatantsSeedOnlyFromEncounterRosterAndAdversaries(): void { $s=$this->source('app/Modules/DungeonMaster/Controllers/InitiativeController.php');self::assertStringContainsString('$encounter->characterIds()',$s);self::assertStringContainsString('$this->rosters->members($campaign)',$s);self::assertStringContainsString('findForOwner(CharacterId::fromString',$s);self::assertStringContainsString('$encounter->adversaries()',$s); }
    public function testCharacterHpAndInitiativeAreSeededWithoutMutatingCharacter(): void { $s=$this->source('app/Modules/DungeonMaster/Controllers/InitiativeController.php');self::assertStringContainsString('$c->hitPoints()',$s);self::assertStringContainsString('$c->initiative()->value()',$s);self::assertStringNotContainsString('$this->characters->update',$s); }
    public function testInitiativeSupportsSortRoundsTurnsConditionsAndCompletion(): void { $s=$this->source('app/Modules/DungeonMaster/Controllers/InitiativeController.php');self::assertStringContainsString("\$action==='sort'",$s);self::assertStringContainsString("\$action==='advance'",$s);self::assertStringContainsString("\$action==='complete'",$s);self::assertStringContainsString("'conditions'",$s);self::assertStringContainsString('Encounter::STATUS_COMPLETED',$s); }
    public function testInitiativeUsesDedicatedCapabilityAndNonce(): void { $r=$this->source('app/Modules/DungeonMaster/Requests/SaveInitiativeRequest.php');$f=$this->source('app/Providers/FrontendServiceProvider.php');$v=$this->source('app/Modules/DungeonMaster/Views/initiative/index.php');self::assertStringContainsString("current_user_can('gmrc_manage_campaigns')",$r);self::assertStringContainsString('gmrc_dm_initiative_',$f);self::assertStringContainsString('gmrc_dm_initiative_',$v); }
    public function testInitiativeRollUsesSecureD20Principle(): void { $s=$this->source('assets/js/modules/dungeon-master/initiative-table.js');self::assertStringContainsString('window.crypto.getRandomValues',$s);self::assertStringContainsString('secureD20',$s);self::assertStringContainsString('data-roll-initiative',$s); }
    public function testInitiativeUsesSafeDmWorkspaceAndAccessibilityFallbacks(): void { $s=$this->source('assets/css/modules/dungeon-master/initiative-table.css');$c=preg_replace('/\s+/','',$s);self::assertStringContainsString('.gmrc-content:has(>.gmrc-initiative-table)',$c);self::assertStringNotContainsString('.gmrc-app-main:has(',$s);self::assertStringContainsString('forced-colors:active',$c);self::assertStringContainsString('prefers-reduced-transparency:reduce',$c);self::assertStringContainsString('background-attachment:scroll',$c); }
    public function testDocumentationRecordsInitiativeCheckpoint(): void { $s=$this->source('docs/GuildArchives/Development/DungeonMasterPhase315.md');self::assertStringContainsString('III.15.5 — The Initiative Table',$s);self::assertStringContainsString('3,435 tests',$s);self::assertStringContainsString('11,427 assertions',$s);self::assertStringContainsString('Phase III.15.6',$s); }
    private function source(string $path): string { $s=file_get_contents($this->root.'/'.$path);self::assertIsString($s);return $s; }
}
