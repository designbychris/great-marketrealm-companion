<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class KeeperWorkspaceFoundationRegressionTest extends TestCase
{
    public function testCampaignFirstDeskMovesCertifiedInstrumentsIntoCommandCentre(): void
    {
        $desk = $this->source('app/Modules/DungeonMaster/Views/index.php');
        $campaign = $this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');

        self::assertStringContainsString('Your Campaigns', $desk);
        self::assertStringContainsString('Start New Campaign', $desk);
        self::assertStringNotContainsString('Choose Campaign', $desk);
        foreach (['Session Ledger', 'Encounter Board', 'Player Roster', 'Monster Ledger', 'Campaign Journal', 'Keeper’s Gazetteer', 'Dramatis Personae', 'Evidence Register'] as $instrument) {
            self::assertStringContainsString($instrument, $campaign);
        }
        self::assertStringContainsString('No campaign selection required.', $campaign);
        self::assertDoesNotMatchRegularExpression('/(?:Phase\s+)?(?:III|IV)\.\d+(?:\.\d+)*(?:[A-Z])?/i', $desk);
        self::assertDoesNotMatchRegularExpression('/(?:Phase\s+)?(?:III|IV)\.\d+(?:\.\d+)*(?:[A-Z])?/i', $campaign);
    }

    public function testWorkspaceCatalogueNamesThePlannedPreparationInstruments(): void
    {
        $catalogue = $this->source('app/Modules/DungeonMaster/Services/KeeperWorkspace.php');

        foreach (['Keeper’s Gazetteer', 'Dramatis Personae', 'Evidence Register', 'Conspiracy Board', 'Cartographer’s Bench'] as $tool) {
            self::assertStringContainsString($tool, $catalogue);
        }

        self::assertStringContainsString("'key' => 'evidence-register'", $catalogue);
        self::assertMatchesRegularExpression("/'key' => 'evidence-register'.*?'status' => 'open'.*?'route' => 'dungeon-master\/campaigns'/s", $catalogue);

        foreach (['III.17.2', 'III.17.3', 'III.17.4', 'III.17.5', 'IV.36'] as $phase) {
            self::assertStringContainsString($phase, $catalogue);
        }
    }

    public function testPlanningVocabularyIsExplicitAndReusable(): void
    {
        $types = $this->source('app/Modules/DungeonMaster/Planning/PlanningRecordType.php');

        foreach (['location', 'person', 'faction', 'evidence', 'thread'] as $type) {
            self::assertStringContainsString("'{$type}'", $types);
        }
    }

    public function testWorkspaceFoundationDoesNotPrematurelyAddPersistenceOrRoutes(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        $catalogue = $this->source('app/Modules/DungeonMaster/Services/KeeperWorkspace.php');

        self::assertStringNotContainsString('/gazetteer', $routes);
        self::assertStringContainsString('/conspiracy-board', $routes);
        self::assertStringContainsString('intentionally contains no persistence', $catalogue);
    }

    public function testControllerReceivesWorkspaceCatalogueThroughContainer(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/DungeonMasterController.php');
        $provider = $this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');

        self::assertStringContainsString('private KeeperWorkspace $workspace', $controller);
        self::assertStringContainsString("'forthcomingTools' => \$this->workspace->forthcomingTools()", $controller);
        self::assertStringContainsString('singleton(KeeperWorkspace::class)', $provider);
        self::assertStringContainsString('$c->make(KeeperWorkspace::class)', $provider);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
