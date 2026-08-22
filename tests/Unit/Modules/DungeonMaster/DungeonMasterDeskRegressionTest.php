<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use GreatMarketrealmCompanion\Core\View\ViewFinder;
use PHPUnit\Framework\TestCase;

final class DungeonMasterDeskRegressionTest extends TestCase
{
    public function testDungeonMasterKingdomIsInstalled(): void
    {
        $provider = $this->source('app/Providers/KingdomServiceProvider.php');
        $kingdom = $this->source('app/Kingdoms/DungeonMasterKingdom.php');

        self::assertStringContainsString('new DungeonMasterKingdom($this->app)', $provider);
        self::assertStringContainsString("return 'dungeon-master';", $kingdom);
        self::assertStringContainsString('DungeonMasterServiceProvider::class', $kingdom);
    }

    public function testDungeonMasterNavigationIsCapabilityAware(): void
    {
        $kingdom = $this->source('app/Kingdoms/DungeonMasterKingdom.php');

        self::assertStringContainsString('GuildRoleRegistrar::MANAGE_CAMPAIGNS', $kingdom);
        self::assertStringContainsString("current_user_can('manage_options')", $kingdom);
        self::assertStringContainsString("\"Dungeon Master's Desk\"", $kingdom);
        self::assertStringContainsString('Icons::DUNGEON_MASTER', $kingdom);
    }

    public function testDeskRouteUsesDedicatedController(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');

        self::assertStringContainsString("'/dungeon-master'", $routes);
        self::assertStringContainsString("[DungeonMasterController::class, 'index']", $routes);
    }

    public function testDirectRequestsAreProtectedServerSide(): void
    {
        $access = $this->source('app/Modules/DungeonMaster/Services/DungeonMasterAccess.php');
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/DungeonMasterController.php');

        self::assertStringContainsString('GuildRoleRegistrar::MANAGE_CAMPAIGNS', $access);
        self::assertStringContainsString("current_user_can('manage_options')", $access);
        self::assertStringContainsString('if (! $this->access->allows())', $controller);
        self::assertStringContainsString('status_header(403)', $controller);
        self::assertStringContainsString("View::make('dungeonmaster.forbidden')", $controller);
    }

    public function testDungeonMasterViewsResolveOnCaseSensitiveFilesystems(): void
    {
        $root = dirname(__DIR__, 4) . '/';
        $finder = new ViewFinder($root);

        self::assertSame(
            $root . 'app/Modules/DungeonMaster/Views/index.php',
            $finder->find('dungeonmaster.index')
        );

        self::assertSame(
            $root . 'app/Modules/DungeonMaster/Views/forbidden.php',
            $finder->find('dungeonmaster.forbidden')
        );
    }

    public function testDeskStylesAreActuallyEnqueuedInSignedInCompanion(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString('enqueueDungeonMasterDesk()', $frontend);
        self::assertStringContainsString('gmrc-dungeon-master-desk', $frontend);
        self::assertStringContainsString('dungeon-master/dungeon-master-desk.css', $frontend);
    }

    public function testDeskStartsWithCampaignAndExistingGuildRecords(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/index.php');

        self::assertStringContainsString('Campaign Register', $view);
        self::assertStringContainsString('Session Ledger', $view);
        self::assertStringContainsString('Encounter Board', $view);
        self::assertStringContainsString('Player Roster', $view);
        self::assertStringContainsString('Open existing Guild records', $view);
    }

    public function testDeskCssIncludesResponsiveAndAccessibilityCoverage(): void
    {
        $css = $this->source('assets/css/modules/dungeon-master/dungeon-master-desk.css');

        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('@media (max-width: 860px)', $css);
        self::assertStringContainsString('@media (forced-colors: active)', $css);
        self::assertStringContainsString('@media (prefers-reduced-transparency: reduce)', $css);
        self::assertStringContainsString('@supports not (backdrop-filter: blur(1px))', $css);
    }

    public function testPhaseDocumentationNamesCampaignRegisterAsNextSlice(): void
    {
        $docs = $this->source('docs/GuildArchives/Development/DungeonMasterPhase315.md');

        self::assertStringContainsString("Phase III.15 — The Dungeon Master's Desk", $docs);
        self::assertStringContainsString('Phase III.15.1 — The Campaign Register', $docs);
        self::assertStringContainsString('3,385 tests', $docs);
        self::assertStringContainsString('11,148 assertions', $docs);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
