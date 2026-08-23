<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class DungeonMasterFinalCertificationRegressionTest extends TestCase
{
    public function testDeskPresentsEveryCertifiedLedgerAsOpen(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/index.php');
        foreach (['Campaign Register', 'Session Ledger', 'Encounter Board', 'Player Roster', 'Monster Ledger', 'Campaign Journal'] as $label) {
            self::assertStringContainsString($label, $view);
        }
        self::assertStringNotContainsString('Coming soon', $view);
    }

    public function testCommandCentreProvidesExplicitRoutesBackToRegisterAndDesk(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');
        self::assertStringContainsString("\$registerUrl = \$route('dungeon-master/campaigns')", $view);
        self::assertStringContainsString("\$deskUrl = \$route('dungeon-master')", $view);
        self::assertStringContainsString('Campaign Register', $view);
        self::assertStringContainsString('Dungeon Master’s Desk', $view);
    }

    public function testArchivedCommandCentreDoesNotAdvertiseCampaignEditing(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');
        self::assertStringContainsString('$archived = $campaign->isArchived()', $view);
        self::assertStringContainsString('if (! $archived)', $view);
        self::assertStringContainsString('preserved as read-only history', $view);
    }

    public function testArchivedCampaignUpdateIsBlockedServerSide(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/CampaignController.php');
        self::assertStringContainsString('$this->assertActive($campaign)', $controller);
        self::assertStringContainsString('Archived campaigns are preserved as read-only history.', $controller);
    }

    public function testCampaignRegisterReturnsToDungeonMasterDesk(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/campaigns/index.php');
        self::assertStringContainsString("'dungeon-master'", $view);
        self::assertStringContainsString('Dungeon Master’s Desk', $view);
    }

    public function testCampaignOwnedLedgersRetainBackNavigation(): void
    {
        foreach ([
            'app/Modules/DungeonMaster/Views/players/index.php',
            'app/Modules/DungeonMaster/Views/sessions/index.php',
            'app/Modules/DungeonMaster/Views/encounters/index.php',
            'app/Modules/DungeonMaster/Views/journal/index.php',
        ] as $path) {
            self::assertStringContainsString('Back to', $this->source($path), $path);
        }
    }

    public function testCombatConsoleRetainsArchivedReadOnlyBoundary(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/initiative/index.php');
        self::assertStringContainsString('$archived = $campaign->isArchived()', $view);
        self::assertStringContainsString('read-only combat history', $view);
        self::assertStringContainsString('← Encounter', $view);
    }

    public function testDungeonMasterAccessStillIncludesDmAndAdministratorCapabilities(): void
    {
        $access = $this->source('app/Modules/DungeonMaster/Services/DungeonMasterAccess.php');
        self::assertStringContainsString('GuildRoleRegistrar::MANAGE_CAMPAIGNS', $access);
        self::assertStringContainsString("current_user_can('manage_options')", $access);
    }

    public function testFinalPhaseDocumentationSealsTheProgrammeAndNamesPhaseSixteen(): void
    {
        $docs = $this->source('docs/GuildArchives/Development/DungeonMasterPhase315.md');
        self::assertStringContainsString('Phase III.15.10 — Dungeon Master’s Desk Final Polish & Certification', $docs);
        self::assertStringContainsString('3,481 tests', $docs);
        self::assertStringContainsString('11,714 assertions', $docs);
        self::assertStringContainsString('Phase III.16 — Companion Administration & Security', $docs);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
