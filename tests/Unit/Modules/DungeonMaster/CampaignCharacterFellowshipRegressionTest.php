<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class CampaignCharacterFellowshipRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testRoutesCoverPlayerNominationAndCampaignFellowshipLifecycle(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        self::assertStringContainsString("'/active-campaigns/{id}/adventurer'", $routes);
        self::assertStringContainsString("ActiveCampaignController::class,'assign'", $routes);
        self::assertStringContainsString("ActiveCampaignController::class,'clear'", $routes);
        self::assertStringContainsString("'/dungeon-master/campaigns/{id}/fellowship'", $routes);
        self::assertStringContainsString("PlayerRosterController::class,'foundFellowship'", $routes);
        self::assertStringContainsString("PlayerRosterController::class,'linkFellowship'", $routes);
        self::assertStringContainsString("PlayerRosterController::class,'unlinkFellowship'", $routes);
    }

    public function testRosterNominationIsSingleCharacterAndCanBeCleared(): void
    {
        $repository = $this->source('app/Modules/DungeonMaster/Repositories/CampaignRosterRepository.php');
        self::assertStringContainsString('function assignCharacter(', $repository);
        self::assertStringContainsString("\$roster[\$key]['character_ids'] = [\$characterId]", $repository);
        self::assertStringContainsString('function clearCharacterAssignment(', $repository);
        self::assertStringContainsString("\$roster[\$key]['character_ids'] = []", $repository);
    }

    public function testPlayerCanOnlyNominateTheirOwnCharacterInJoinedActiveCampaign(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/ActiveCampaignController.php');
        self::assertStringContainsString('playerCampaign($id, $playerId)', $controller);
        self::assertStringContainsString('findForOwner($characterId, $playerId)', $controller);
        self::assertStringContainsString('assignCharacter(', $controller);
        self::assertStringContainsString('$campaign->isArchived()', $controller);
        self::assertStringNotContainsString('attachCharacter($campaign, $playerId', $controller);
    }

    public function testActiveCampaignsProvidesNominationUiAndCampaignFellowshipContext(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/active-campaigns/index.php');
        self::assertStringContainsString('Your Campaign Adventurer', $view);
        self::assertStringContainsString('Nominate adventurer', $view);
        self::assertStringContainsString('Change adventurer', $view);
        self::assertStringContainsString('Clear nomination', $view);
        self::assertStringContainsString('gmrc_active_campaign_character_', $view);
        self::assertStringContainsString('Campaign Fellowship', $view);
        self::assertStringNotContainsString('Phase III.', $view);
    }

    public function testCampaignFellowshipLinkIsSeparateFromPartyRecord(): void
    {
        $repository = $this->source('app/Modules/DungeonMaster/Repositories/CampaignFellowshipRepository.php');
        self::assertStringContainsString("'_gmrc_campaign_fellowship'", $repository);
        self::assertStringContainsString("'party_id' => \$party->id()->value()", $repository);
        self::assertStringContainsString("'owner_id' => \$party->ownerId()->value()", $repository);
        self::assertStringContainsString('delete_post_meta(', $repository);
        self::assertStringNotContainsString('delete($party', $repository);
    }

    public function testFoundingFellowshipSeedsNominatedAdventurersAndEnablesCertifiedSync(): void
    {
        $service = $this->source('app/Modules/DungeonMaster/Services/CampaignFellowshipService.php');
        self::assertStringContainsString('assignedCharacterIds($campaign)', $service);
        self::assertStringContainsString('$party->addMember(CharacterId::fromString($characterId))', $service);
        self::assertStringContainsString('$this->parties->save($party)', $service);
        self::assertStringContainsString('$this->links->link($campaign, $party, $characterIds)', $service);
        self::assertStringContainsString('$this->membershipSync->synchronize($campaign)', $service);
        self::assertStringNotContainsString('addPlayer(', $service);
    }

    public function testDungeonMasterCanFoundOrLinkWithCertifiedMembershipSynchronisation(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/players/index.php');
        self::assertStringContainsString('Found Fellowship from roster', $view);
        self::assertStringContainsString('Link an existing Fellowship', $view);
        self::assertStringContainsString('Release Fellowship link', $view);
        self::assertStringContainsString('kept in step automatically', $view);
        self::assertStringContainsString('pre-existing Fellowship members are never removed', $view);
        self::assertStringContainsString('Market Pass is the normal invitation route', $view);
    }

    public function testCrossAccountCampaignFellowshipPresentationAndNoncesAreGuarded(): void
    {
        $characters = $this->source('app/Modules/Characters/Repositories/CharacterRepository.php');
        $presenter = $this->source('app/Modules/Parties/Presenters/FellowshipPresenter.php');
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        self::assertStringContainsString('findAcrossOwners(CharacterId $id)', $characters);
        self::assertStringContainsString('findAcrossOwners($membershipId)', $presenter);
        self::assertStringContainsString('gmrc_active_campaign_character_', $frontend);
        self::assertStringContainsString('gmrc_dm_campaign_fellowship_', $frontend);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
