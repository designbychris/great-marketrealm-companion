<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class KeeperWorkspaceFoundationRegressionTest extends TestCase
{
    public function testDeskGrowsWithoutReplacingCertifiedLedgers(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/index.php');

        foreach (['Campaign Register', 'Session Ledger', 'Encounter Board', 'Player Roster', 'Monster Ledger', 'Campaign Journal'] as $ledger) {
            self::assertStringContainsString($ledger, $view);
        }

        self::assertStringContainsString('Phase III.17 · The Keeper’s Workshop', $view);
        self::assertStringContainsString('The Desk Grows', $view);
        self::assertStringContainsString('campaign records remain the source of truth', $view);
    }

    public function testWorkspaceCatalogueNamesThePlannedPreparationInstruments(): void
    {
        $catalogue = $this->source('app/Modules/DungeonMaster/Services/KeeperWorkspace.php');

        foreach (['Keeper’s Gazetteer', 'Dramatis Personae', 'Evidence Register', 'Conspiracy Board', 'Cartographer’s Bench'] as $tool) {
            self::assertStringContainsString($tool, $catalogue);
        }

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
        self::assertStringNotContainsString('/conspiracy-board', $routes);
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
