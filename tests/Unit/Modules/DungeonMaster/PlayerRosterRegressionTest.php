<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class PlayerRosterRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testPlayerRosterRoutesAreCampaignScoped(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        $compact = preg_replace('/\s+/', '', $routes);

        self::assertStringContainsString(
            "'/dungeon-master/campaigns/{id}/players'",
            $compact
        );
        self::assertStringContainsString(
            "'/dungeon-master/campaigns/{id}/players/{playerId}'",
            $compact
        );
        self::assertStringContainsString(
            "'/dungeon-master/campaigns/{id}/players/{playerId}/characters/{characterId}'",
            $compact
        );
        self::assertStringContainsString('PlayerRosterController::class', $routes);
    }

    public function testRosterPersistsOnOwnedPrivateCampaignRecord(): void
    {
        $roster = $this->source(
            'app/Modules/DungeonMaster/Repositories/CampaignRosterRepository.php'
        );
        $campaigns = $this->source(
            'app/Modules/DungeonMaster/Repositories/CampaignRepository.php'
        );

        self::assertStringContainsString('_gmrc_campaign_roster', $roster);
        self::assertStringContainsString('postIdForOwner(', $roster);
        self::assertStringContainsString('postIdForOwner(string $id, int $ownerId)', $campaigns);
        self::assertStringContainsString("'author'=>\$ownerId", str_replace(' ', '', $campaigns));
    }

    public function testOnlyExistingPlayerGuildAccountsCanBeRostered(): void
    {
        $controller = $this->source(
            'app/Modules/DungeonMaster/Controllers/PlayerRosterController.php'
        );

        self::assertStringContainsString("get_user_by('login', \$identity)", $controller);
        self::assertStringContainsString("get_user_by('email', \$identity)", $controller);
        self::assertStringContainsString("user_can(\$user->ID, 'gmrc_access_companion')", $controller);
        self::assertStringContainsString('AccountType::PLAYER', $controller);
        self::assertStringContainsString('GuildProfile::accountType', $controller);
    }

    public function testCharacterAttachmentVerifiesPlayerAuthorship(): void
    {
        $controller = $this->source(
            'app/Modules/DungeonMaster/Controllers/PlayerRosterController.php'
        );
        $characters = $this->source(
            'app/Modules/Characters/Repositories/CharacterRepository.php'
        );

        self::assertStringContainsString('findForOwner(', $controller);
        self::assertStringContainsString('allForOwner($playerId)', $controller);
        self::assertStringContainsString('public function allForOwner(int $ownerId)', $characters);
        self::assertStringContainsString('public function findForOwner(', $characters);
        self::assertStringContainsString("'author' => \$ownerId", $characters);
    }

    public function testRosterCommandsUseCampaignScopedNonceAndDmCapability(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $request = $this->source(
            'app/Modules/DungeonMaster/Requests/AddRosterPlayerRequest.php'
        );
        $view = $this->source(
            'app/Modules/DungeonMaster/Views/players/index.php'
        );

        self::assertStringContainsString('gmrc_dm_roster_', $frontend);
        self::assertStringContainsString('gmrc_dm_roster_', $view);
        self::assertStringContainsString("current_user_can('gmrc_manage_campaigns')", $request);
    }

    public function testCampaignAndDeskOpenThePlayerRoster(): void
    {
        $campaign = $this->source(
            'app/Modules/DungeonMaster/Views/campaigns/show.php'
        );
        $desk = $this->source(
            'app/Modules/DungeonMaster/Views/index.php'
        );

        self::assertStringContainsString('Open Player Roster', $campaign);
        self::assertStringContainsString('/players', $campaign);
        self::assertStringContainsString('Ledger IV · Open', $desk);
        self::assertStringContainsString('Choose Campaign', $desk);
    }

    public function testRosterKeepsDmWorkspaceScopingAndAccessibilityFallbacks(): void
    {
        $css = $this->source(
            'assets/css/modules/dungeon-master/player-roster.css'
        );
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $compact = preg_replace('/\s+/', '', $css);

        self::assertStringContainsString('.gmrc-content:has(>.gmrc-player-roster)', $compact);
        self::assertStringNotContainsString('.gmrc-app-main:has(', $css);
        self::assertStringContainsString('dungeon-master-desk-background.png', $css);
        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('forced-colors:active', $compact);
        self::assertStringContainsString('prefers-reduced-transparency:reduce', $compact);
        self::assertStringContainsString('background-attachment:scroll', $compact);
        self::assertStringContainsString('gmrc-player-roster', $frontend);
        self::assertStringContainsString('dungeon-master/player-roster.css', $frontend);
    }

    public function testPhaseDocumentationRecordsCertifiedRosterCheckpoint(): void
    {
        $docs = $this->source(
            'docs/GuildArchives/Development/DungeonMasterPhase315.md'
        );

        self::assertStringContainsString('III.15.2 — The Player Roster', $docs);
        self::assertStringContainsString('3,409 tests', $docs);
        self::assertStringContainsString('11,246 assertions', $docs);
        self::assertStringContainsString('Phase III.15.3 — The Session Ledger', $docs);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
