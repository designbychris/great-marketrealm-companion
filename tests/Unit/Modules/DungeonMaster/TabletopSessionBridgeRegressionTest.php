<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class TabletopSessionBridgeRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function test_companion_owns_stable_tabletop_to_campaign_link(): void
    {
        $repository = $this->source('app/Modules/DungeonMaster/Repositories/CampaignTabletopLinkRepository.php');
        self::assertStringContainsString("'_gmrc_campaign_tabletop_id'", $repository);
        self::assertStringContainsString('campaignForTable(string $tableId, int $ownerId)', $repository);
        self::assertStringContainsString('postIdForOwner', $repository);
        self::assertStringContainsString('allForOwner', $repository);
    }

    public function test_bridge_projects_campaign_and_linked_fellowship_to_tabletop(): void
    {
        $bridge = $this->source('app/Modules/DungeonMaster/Integration/TabletopSessionBridge.php');
        self::assertStringContainsString('$this->fellowships->linked($campaign)', $bridge);
        self::assertStringContainsString("'fellowship_id'", $bridge);
        self::assertStringContainsString("'fellowship_name'", $bridge);
        self::assertStringContainsString('projectCampaign', $bridge);
    }

    public function test_tabletop_sessions_are_upserted_without_overwriting_dm_notes(): void
    {
        $bridge = $this->source('app/Modules/DungeonMaster/Integration/TabletopSessionBridge.php');
        $model = $this->source('app/Modules/DungeonMaster/Models/Session.php');

        self::assertStringContainsString('findByTabletopSessionId', $bridge);
        self::assertStringContainsString('findUnlinkedByNumber', $bridge);
        self::assertStringContainsString('synchroniseTabletop', $bridge);
        self::assertStringContainsString('$this->prepNotes', $model);
        self::assertStringContainsString('$this->recap', $model);
    }

    public function test_session_ledger_persists_authoritative_play_timestamps_and_duration(): void
    {
        $model = $this->source('app/Modules/DungeonMaster/Models/Session.php');
        $repository = $this->source('app/Modules/DungeonMaster/Repositories/SessionRepository.php');
        $show = $this->source('app/Modules/DungeonMaster/Views/sessions/show.php');

        self::assertStringContainsString("STATUS_IN_PROGRESS = 'in-progress'", $model);
        self::assertStringContainsString("'_gmrc_session_started_at'", $repository);
        self::assertStringContainsString("'_gmrc_session_ended_at'", $repository);
        self::assertStringContainsString("'_gmrc_session_duration_seconds'", $repository);
        self::assertStringContainsString('Certified automatically from the linked Great MarketRealm Tabletop Session.', $show);
    }

    public function test_completed_tabletop_session_is_projected_to_linked_fellowship_chronicle(): void
    {
        $bridge = $this->source('app/Modules/DungeonMaster/Integration/TabletopSessionBridge.php');
        self::assertStringContainsString('writeFellowshipChronicle', $bridge);
        self::assertStringContainsString('PartyChronicleEntryType::deed()', $bridge);
        self::assertStringContainsString('$this->parties->save($fellowship)', $bridge);
        self::assertStringContainsString('if ($endedAt instanceof DateTimeImmutable)', $bridge);
    }

    public function test_company_chronicle_projection_contains_only_safe_play_facts(): void
    {
        $bridge = $this->source('app/Modules/DungeonMaster/Integration/TabletopSessionBridge.php');
        self::assertStringContainsString('The Fellowship gathered for Session %d of %s.', $bridge);
        self::assertStringContainsString("'tabletop_session_id' => \$session->tabletopSessionId()", $bridge);
        self::assertStringContainsString('$content = $preview;', $bridge);
        self::assertStringContainsString("'recap' => \$session->recap()", $bridge);
        self::assertStringContainsString("'contributions' => \$session->contributions()", $bridge);
        self::assertStringNotContainsString('$session->prepNotes()', $bridge);
    }

    public function test_company_chronicle_upserts_by_immutable_tabletop_session_identity(): void
    {
        $chronicle = $this->source('app/Modules/Parties/Models/PartyChronicle.php');
        $entry = $this->source('app/Modules/Parties/Models/PartyChronicleEntry.php');
        self::assertStringContainsString("sourceValue('tabletop_session_id')", $chronicle);
        self::assertStringContainsString('refreshCertifiedRecord', $chronicle);
        self::assertStringContainsString("'source' => \$this->source", $entry);
    }

    public function test_dungeon_master_provider_registers_cross_plugin_contracts(): void
    {
        $provider = $this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');
        self::assertStringContainsString("'gmrt_companion_campaign_choices'", $provider);
        self::assertStringContainsString("'gmrt_companion_campaign_for_table'", $provider);
        self::assertStringContainsString("'gmrt_companion_link_campaign'", $provider);
        self::assertStringContainsString("'gmrt_companion_sync_table_session'", $provider);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
