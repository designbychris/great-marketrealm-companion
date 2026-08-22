<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class CombatConsoleRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testCombatConsoleExtendsExistingInitiativeContract(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        $view = $this->source('app/Modules/DungeonMaster/Views/initiative/index.php');
        self::assertStringContainsString('/initiative', $routes);
        self::assertStringContainsString('The Combat Console', $view);
        self::assertStringContainsString('gmrc_dm_initiative_', $view);
    }

    public function testCombatConsoleAddsRewindAndUnexpectedCombatants(): void
    {
        $request = $this->source('app/Modules/DungeonMaster/Requests/SaveInitiativeRequest.php');
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/InitiativeController.php');
        self::assertStringContainsString('rewind,reset,complete,add,remove', $request);
        self::assertStringContainsString("\$action === 'rewind'", $controller);
        self::assertStringContainsString('addCombatant', $controller);
        self::assertStringContainsString('removeCombatant', $controller);
        self::assertStringContainsString('wp_generate_uuid4()', $controller);
    }

    public function testCombatantsPersistTempHpStateConcentrationAndDmNotes(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/InitiativeController.php');
        foreach (['temp_hp', 'state', 'concentrating', 'notes'] as $field) {
            self::assertStringContainsString("'{$field}'", $controller);
        }
        self::assertStringContainsString('$hp->temporary()', $controller);
        self::assertStringContainsString('sanitize_textarea_field', $controller);
    }

    public function testQuickDamageConsumesTempHpAndHealingRespectsMaximum(): void
    {
        $script = $this->source('assets/js/modules/dungeon-master/initiative-table.js');
        self::assertStringContainsString("data-quick-vital", $script);
        self::assertStringContainsString('Math.min(temp, amount)', $script);
        self::assertStringContainsString("mode === 'damage'", $script);
        self::assertStringContainsString("mode === 'heal'", $script);
        self::assertStringContainsString('Math.min(integerValue(maximum), hp + amount)', $script);
    }

    public function testConditionChipsRemainBackedByPersistedConditionText(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/initiative/index.php');
        $script = $this->source('assets/js/modules/dungeon-master/initiative-table.js');
        self::assertStringContainsString('data-condition=', $view);
        self::assertStringContainsString('data-conditions-input', $view);
        self::assertStringContainsString('aria-pressed=', $view);
        self::assertStringContainsString("values.join(', ')", $script);
    }

    public function testCombatLogPersistsAndIsBounded(): void
    {
        $model = $this->source('app/Modules/DungeonMaster/Models/InitiativeTable.php');
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/InitiativeController.php');
        self::assertStringContainsString('private array $log', $model);
        self::assertStringContainsString('array_slice($log, -80)', $model);
        self::assertStringContainsString("'log' =>", $controller);
        self::assertStringContainsString('logChanges', $controller);
        self::assertStringContainsString("'recorded_at' => current_time('mysql')", $controller);
    }

    public function testCombatConsoleStillDoesNotMutatePlayerCharacterLedger(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/InitiativeController.php');
        self::assertStringContainsString('findForOwner', $controller);
        self::assertStringNotContainsString('$this->characters->update(', $controller);
        self::assertStringNotContainsString('$this->characters->save(', $controller);
    }

    public function testDestructiveConsoleActionsRequireConfirmation(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/initiative/index.php');
        $script = $this->source('assets/js/modules/dungeon-master/initiative-table.js');
        self::assertStringContainsString('data-confirm=', $view);
        self::assertStringContainsString('Complete this Encounter', $view);
        self::assertStringContainsString('window.confirm', $script);
    }

    public function testCombatConsoleRetainsNavigationSafeAccessibleWorkspace(): void
    {
        $css = $this->source('assets/css/modules/dungeon-master/initiative-table.css');
        $compact = preg_replace('/\s+/', '', $css);
        self::assertIsString($compact);
        self::assertStringContainsString('.gmrc-content:has(>.gmrc-initiative-table)', $compact);
        self::assertStringNotContainsString('.gmrc-app-main:has(', $css);
        self::assertStringContainsString('prefers-reduced-transparency:reduce', $compact);
        self::assertStringContainsString('forced-colors:active', $compact);
        self::assertStringContainsString('background-attachment:scroll', $compact);
        self::assertStringContainsString(':focus-visible', $css);
    }

    public function testDocumentationRecordsCombatConsoleCheckpoint(): void
    {
        $docs = $this->source('docs/GuildArchives/Development/DungeonMasterPhase315.md');
        self::assertStringContainsString('III.15.7 — The Combat Console', $docs);
        self::assertStringContainsString('3,458 tests', $docs);
        self::assertStringContainsString('11,579 assertions', $docs);
        self::assertStringContainsString('Phase III.15.8', $docs);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
