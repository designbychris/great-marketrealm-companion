<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class ReadOnlyCharacterLedgerRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testCampaignRouteExposesDmReadOnlyCharacterLedger(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        self::assertStringContainsString("/dungeon-master/campaigns/{id}/characters/{characterId}", $routes);
        self::assertStringContainsString('ReadOnlyCharacterController::class', $routes);
    }

    public function testLedgerRequiresDungeonMasterAccessAndOwnedCampaign(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/ReadOnlyCharacterController.php');
        self::assertStringContainsString('$this->access->allows()', $controller);
        self::assertStringContainsString('findForOwner(', $controller);
        self::assertStringContainsString('get_current_user_id()', $controller);
    }

    public function testArchivedCampaignsDoNotGrantLiveCharacterAccess(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/ReadOnlyCharacterController.php');
        self::assertStringContainsString('$campaign->isArchived()', $controller);
        self::assertStringContainsString('do not grant live Character Ledger access', $controller);
    }

    public function testCharacterMustCurrentlyAppearInCampaignRoster(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/ReadOnlyCharacterController.php');
        self::assertStringContainsString('rosteredOwnerId($campaign, $characterId)', $controller);
        self::assertStringContainsString("in_array(\$characterId, \$membership['character_ids'], true)", $controller);
        self::assertStringContainsString('not currently assigned to this Campaign Roster', $controller);
    }

    public function testCharacterOwnershipIsReverifiedAgainstRosteredPlayer(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/ReadOnlyCharacterController.php');
        self::assertStringContainsString('CharacterId::fromString($characterId)', $controller);
        self::assertStringContainsString('$ownerId', $controller);
        self::assertStringNotContainsString('findAcrossOwners(', $controller);
    }

    public function testRosterOnlyLinksCurrentlyAttachedCharacters(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/players/index.php');
        self::assertStringContainsString('if ($isLinked && ! $campaign->isArchived())', $view);
        self::assertStringContainsString('View Character Ledger', $view);
        self::assertStringContainsString("'/characters/' . \$characterId", $view);
    }

    public function testReadOnlyProjectionReusesCanonicalCharacterLedgerAssembly(): void
    {
        $controller = $this->source('app/Modules/Characters/Controllers/CharacterController.php');
        self::assertStringContainsString('renderReadOnlyForCampaign(', $controller);
        self::assertStringContainsString("'dungeonmaster.characters.show'", $controller);
        self::assertStringContainsString('private function renderLedger(', $controller);
    }

    public function testDmLedgerContainsNoMutationFormsOrApplicationCommands(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/characters/show.php');
        self::assertStringNotContainsString('<form', $view);
        self::assertStringNotContainsString('gmrc_app_request', $view);
        self::assertStringNotContainsString('admin-post.php', $view);
        self::assertStringNotContainsString('/edit', $view);
        self::assertStringNotContainsString('/delete', $view);
    }

    public function testDmLedgerExposesEncounterRelevantCharacterData(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/characters/show.php');
        foreach (['Hit Points', 'Abilities & Saving Throws', 'Skills', 'Attacks', 'Spellcasting', 'Equipment', 'Known Languages', 'Tool Proficiencies'] as $label) {
            self::assertStringContainsString($label, $view);
        }
    }

    public function testPrivatePlayerNotesAreExplicitlyExcluded(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/characters/show.php');
        self::assertStringContainsString('Private Player notes are not included', $view);
        self::assertStringNotContainsString('combat-notes', $view);
        self::assertStringNotContainsString('Journal', $view);
    }

    public function testPersistedPortraitIsRenderedWithoutControls(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/characters/show.php');
        self::assertStringContainsString('components.media.illuminated-portrait', $view);
        self::assertStringContainsString("'controlsEnabled' => false", $view);
    }

    public function testControllerIsRegisteredThroughDungeonMasterProvider(): void
    {
        $provider = $this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');
        self::assertStringContainsString('ReadOnlyCharacterController::class', $provider);
        self::assertStringContainsString('$c->make(CharacterController::class)', $provider);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
