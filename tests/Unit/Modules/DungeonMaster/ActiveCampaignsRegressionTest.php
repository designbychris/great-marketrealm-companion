<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class ActiveCampaignsRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testPlayerHasDedicatedActiveCampaignsRoute(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        self::assertStringContainsString("'/active-campaigns'", $routes);
        self::assertStringContainsString("ActiveCampaignController::class,'index'", $routes);
    }

    public function testPlayerCampaignRepositoryResolvesMembershipThroughRoster(): void
    {
        $repository = $this->source('app/Modules/DungeonMaster/Repositories/PlayerCampaignRepository.php');
        self::assertStringContainsString('$this->campaigns->all()', $repository);
        self::assertStringContainsString('$this->rosters->hasPlayer($campaign, $playerId)', $repository);
    }

    public function testActiveCampaignControllerMaintainsPlayerBoundary(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/ActiveCampaignController.php');
        self::assertStringContainsString('AccountType::PLAYER', $controller);
        self::assertStringContainsString("user_can(\$userId, 'gmrc_access_companion')", $controller);
        self::assertStringContainsString('allForPlayer($playerId)', $controller);
        self::assertStringNotContainsString('SessionRepository', $controller);
        self::assertStringNotContainsString('EncounterRepository', $controller);
        self::assertStringNotContainsString('JournalRepository', $controller);
    }

    public function testPlayerPresentationContainsOnlyCampaignMembershipContext(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/ActiveCampaignController.php');
        self::assertStringContainsString("'dungeon_master'", $controller);
        self::assertStringContainsString("'assigned_characters'", $controller);
        self::assertStringContainsString("'characters'", $controller);
        self::assertStringContainsString("'is_archived'", $controller);
        self::assertStringContainsString('$this->rosters->members($campaign)', $controller);
    }

    public function testGuildHallLinksPlayersToActiveCampaigns(): void
    {
        $dashboard = $this->source('app/Modules/Dashboard/Views/index.php');
        self::assertStringContainsString("'active-campaigns'", $dashboard);
        self::assertStringContainsString('Active Campaigns', $dashboard);
        self::assertStringContainsString('Open Active Campaigns', $dashboard);
    }

    public function testActiveCampaignsSurfaceExplainsAssignmentBoundaryAndEmptyState(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/active-campaigns/index.php');
        self::assertStringContainsString('No Campaigns joined yet', $view);
        $this->assertStringContainsString('Your Campaign Adventurer', $view);
        $this->assertStringContainsString('Choose your adventurer', $view);
        $this->assertStringContainsString('Nominate adventurer', $view);
        self::assertStringContainsString('No adventurer nominated yet', $view);
        self::assertStringContainsString('Redeem another Market Pass', $view);
        self::assertStringNotContainsString('Phase III.', $view);
    }

    public function testSuccessfulMarketPassRedemptionFlowsIntoActiveCampaigns(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/MarketPassController.php');
        self::assertStringContainsString('activeCampaignsUrl()', $controller);
        self::assertStringContainsString("'gmrc_route', 'active-campaigns'", $controller);
    }

    public function testActiveCampaignsStylingIsRegisteredAndAccessible(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $css = $this->source('assets/css/modules/dungeon-master/active-campaigns.css');
        self::assertStringContainsString('gmrc-active-campaigns', $frontend);
        self::assertStringContainsString('active-campaigns.css', $frontend);
        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('forced-colors:active', $css);
        self::assertStringContainsString('prefers-reduced-transparency:reduce', $css);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
