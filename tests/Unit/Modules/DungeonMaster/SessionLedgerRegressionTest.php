<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class SessionLedgerRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testSessionRoutesAreNestedUnderOwnedCampaign(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        $compact = preg_replace('/\s+/', '', $routes);

        self::assertStringContainsString("'/dungeon-master/campaigns/{id}/sessions'", $compact);
        self::assertStringContainsString("'/dungeon-master/campaigns/{id}/sessions/create'", $compact);
        self::assertStringContainsString("'/dungeon-master/campaigns/{id}/sessions/{sessionId}'", $compact);
        self::assertStringContainsString('SessionController::class', $routes);
    }

    public function testSessionsUsePrivateFirstClassWordPressPersistence(): void
    {
        $repository = $this->source('app/Modules/DungeonMaster/Repositories/SessionRepository.php');
        $provider = $this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');

        self::assertStringContainsString("POST_TYPE = 'gmrc_session'", $repository);
        self::assertStringContainsString('_gmrc_session_campaign_id', $repository);
        self::assertStringContainsString("'post_author' => \$campaign->ownerId()", $repository);
        self::assertStringContainsString("'post_parent' => \$campaignPostId", $repository);
        self::assertStringContainsString('SessionRepository::POST_TYPE', $provider);
        self::assertStringContainsString("'public'=>false", str_replace(' ', '', $provider));
    }

    public function testSessionRecordHasPermanentIdentityAndChronicleFields(): void
    {
        $model = $this->source('app/Modules/DungeonMaster/Models/Session.php');
        $repository = $this->source('app/Modules/DungeonMaster/Repositories/SessionRepository.php');

        self::assertStringContainsString('Ulid::generate()', $model);
        self::assertStringContainsString('STATUS_PLANNED', $model);
        self::assertStringContainsString('STATUS_PLAYED', $model);
        self::assertStringContainsString('STATUS_CANCELLED', $model);
        self::assertStringContainsString('_gmrc_session_prep_notes', $repository);
        self::assertStringContainsString('_gmrc_session_recap', $repository);
        self::assertStringContainsString('_gmrc_session_attendance', $repository);
    }

    public function testAttendanceIsIntersectedWithCampaignRosterAndAttachedCharacters(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/SessionController.php');

        self::assertStringContainsString('$this->rosters->members($campaign)', $controller);
        self::assertStringContainsString('in_array($playerId, $selectedPlayers, true)', $controller);
        self::assertStringContainsString("array_intersect(\$selectedCharacters, \$allowed)", $controller);
        self::assertStringContainsString('allForOwner($playerId)', $controller);
        self::assertStringContainsString('findForOwner(', $controller);
    }

    public function testSessionMutationsUseDmCapabilityAndCampaignScopedNonce(): void
    {
        $request = $this->source('app/Modules/DungeonMaster/Requests/SaveSessionRequest.php');
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $form = $this->source('app/Modules/DungeonMaster/Views/sessions/_form.php');

        self::assertStringContainsString("current_user_can('gmrc_manage_campaigns')", $request);
        self::assertStringContainsString('gmrc_dm_session_', $frontend);
        self::assertStringContainsString('gmrc_dm_session_', $form);
        self::assertStringContainsString("['POST', 'PUT']", $frontend);
    }

    public function testArchivedCampaignsKeepSessionHistoryReadOnly(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/SessionController.php');
        $index = $this->source('app/Modules/DungeonMaster/Views/sessions/index.php');
        $show = $this->source('app/Modules/DungeonMaster/Views/sessions/show.php');

        self::assertStringContainsString('Archived campaigns have a read-only Session Ledger.', $controller);
        self::assertStringContainsString('preserved as read-only history', $index);
        self::assertStringContainsString("if (! \$campaign->isArchived())", $show);
    }

    public function testCampaignAndDeskOpenSessionLedger(): void
    {
        $campaign = $this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');
        $desk = $this->source('app/Modules/DungeonMaster/Views/index.php');

        self::assertStringContainsString('Open Session Ledger', $campaign);
        self::assertStringContainsString('/sessions', $campaign);
        self::assertStringContainsString('Ledger II · Open', $desk);
        self::assertStringContainsString('Choose Campaign', $desk);
    }

    public function testSessionLedgerUsesSafeDmWorkspaceAndAccessibilityFallbacks(): void
    {
        $css = $this->source('assets/css/modules/dungeon-master/session-ledger.css');
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $compact = preg_replace('/\s+/', '', $css);

        self::assertStringContainsString('.gmrc-content:has(>.gmrc-session-ledger)', $compact);
        self::assertStringNotContainsString('.gmrc-app-main:has(', $css);
        self::assertStringContainsString('dungeon-master-desk-background.png', $css);
        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('forced-colors:active', $compact);
        self::assertStringContainsString('prefers-reduced-transparency:reduce', $compact);
        self::assertStringContainsString('background-attachment:scroll', $compact);
        self::assertStringContainsString('gmrc-session-ledger', $frontend);
        self::assertStringContainsString('dungeon-master/session-ledger.css', $frontend);
    }

    public function testPhaseDocumentationRecordsSessionLedgerCheckpoint(): void
    {
        $docs = $this->source('docs/GuildArchives/Development/DungeonMasterPhase315.md');

        self::assertStringContainsString('III.15.3 — The Session Ledger', $docs);
        self::assertStringContainsString('3,417 tests', $docs);
        self::assertStringContainsString('11,301 assertions', $docs);
        self::assertStringContainsString('Phase III.15.4 — The Encounter Board', $docs);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
