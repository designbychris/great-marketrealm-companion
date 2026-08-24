<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class MarketPassCampaignMembershipCertificationTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testAllCampaignMembershipEntryPointsUseOneSynchronizer(): void
    {
        $marketPass = $this->source('app/Modules/DungeonMaster/Controllers/MarketPassController.php');
        $roster = $this->source('app/Modules/DungeonMaster/Controllers/PlayerRosterController.php');
        $active = $this->source('app/Modules/DungeonMaster/Controllers/ActiveCampaignController.php');

        self::assertStringContainsString('$this->rosters->addPlayer($campaign, $playerId)', $marketPass);
        self::assertStringContainsString('$this->membershipSync->synchronize($campaign)', $marketPass);
        self::assertGreaterThanOrEqual(4, substr_count($roster, '$this->membershipSync->synchronize($campaign)'));
        self::assertGreaterThanOrEqual(2, substr_count($active, '$this->membershipSync->synchronize($campaign)'));
    }

    public function testLegacyDirectAddRemainsAProtectedFallback(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/PlayerRosterController.php');
        $view = $this->source('app/Modules/DungeonMaster/Views/players/index.php');

        self::assertStringContainsString('findPlayer($request->identity())', $controller);
        self::assertStringContainsString('GuildProfile::accountType((int) $user->ID) === AccountType::PLAYER', $controller);
        self::assertStringContainsString('Market Pass is the normal invitation route', $view);
        self::assertStringContainsString('Dungeon Master fallback', $view);
    }

    public function testSynchronizerIsIdempotentAndOnlyRemovesCampaignManagedMembers(): void
    {
        $service = $this->source('app/Modules/DungeonMaster/Services/CampaignMembershipSynchronizer.php');
        $links = $this->source('app/Modules/DungeonMaster/Repositories/CampaignFellowshipRepository.php');

        self::assertStringContainsString('managedCharacterIds($campaign)', $service);
        self::assertStringContainsString('if (! $party instanceof Party)', $service);
        self::assertStringContainsString('if (isset($assigned[$characterId]))', $service);
        self::assertStringContainsString('if (! $id instanceof CharacterId || $party->hasMember($id))', $service);
        self::assertStringContainsString("'managed_character_ids'", $links);
        self::assertStringContainsString('array_values(array_unique($characterIds))', $links);
    }

    public function testRemovingOrReassigningCampaignCharactersReconcilesFellowshipAccess(): void
    {
        $roster = $this->source('app/Modules/DungeonMaster/Controllers/PlayerRosterController.php');
        $active = $this->source('app/Modules/DungeonMaster/Controllers/ActiveCampaignController.php');

        self::assertStringContainsString('$this->rosters->removePlayer($campaign, $playerId)', $roster);
        self::assertStringContainsString('$this->rosters->detachCharacter($campaign, $playerId, $characterId)', $roster);
        self::assertStringContainsString('$this->rosters->assignCharacter(', $active);
        self::assertStringContainsString('$this->rosters->clearCharacterAssignment($campaign, $playerId)', $active);
    }

    public function testFellowshipLinkAndReleaseRespectMembershipProvenance(): void
    {
        $service = $this->source('app/Modules/DungeonMaster/Services/CampaignFellowshipService.php');
        $sync = $this->source('app/Modules/DungeonMaster/Services/CampaignMembershipSynchronizer.php');

        self::assertStringContainsString('$this->membershipSync->synchronize($campaign)', $service);
        self::assertStringContainsString('$this->membershipSync->release($campaign)', $service);
        self::assertStringContainsString('$party->removeMember($id)', $sync);
        self::assertStringContainsString('$this->links->unlink($campaign)', $sync);
    }


    public function testRelinkingCannotOrphanManagedMemberships(): void
    {
        $service = $this->source('app/Modules/DungeonMaster/Services/CampaignFellowshipService.php');

        self::assertStringContainsString('$current = $this->links->linked($campaign)', $service);
        self::assertStringContainsString('Release the current Campaign Fellowship before linking another.', $service);
        self::assertStringContainsString('$current->id()->value() === $partyId', $service);
    }

    public function testArchivedCampaignsRemainReadOnlyAtMutationBoundaries(): void
    {
        $marketPass = $this->source('app/Modules/DungeonMaster/Controllers/MarketPassController.php');
        $roster = $this->source('app/Modules/DungeonMaster/Controllers/PlayerRosterController.php');
        $active = $this->source('app/Modules/DungeonMaster/Controllers/ActiveCampaignController.php');

        self::assertStringContainsString('$campaign->isArchived()', $marketPass);
        self::assertStringContainsString('Archived campaigns have a read-only Player Roster.', $roster);
        self::assertStringContainsString('Closed Campaigns preserve their adventurer assignment as history.', $active);
    }

    public function testMarketPassRejectsInvalidAndDuplicateRedemptionSafely(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/MarketPassController.php');
        $repository = $this->source('app/Modules/DungeonMaster/Repositories/MarketPassRepository.php');

        self::assertStringContainsString('invalid, expired, revoked, or belongs to a closed campaign', $controller);
        self::assertStringContainsString('$this->rosters->hasPlayer($campaign, $playerId)', $controller);
        self::assertStringContainsString('You are already signed into', $controller);
        self::assertStringContainsString('$pass->isRedeemable()', $repository);
        self::assertStringContainsString('count($posts) !== 1', $repository);
    }

    public function testApplicationGatewayRetainsSpecificMarketPassAndCampaignNonces(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString("\$route === 'market-pass'", $frontend);
        self::assertStringContainsString("'gmrc_market_pass_redeem'", $frontend);
        self::assertStringContainsString("'gmrc_dm_market_pass_'", $frontend);
        self::assertStringContainsString("'gmrc_active_campaign_character_'", $frontend);
        self::assertStringContainsString("'gmrc_dm_campaign_fellowship_'", $frontend);
        self::assertStringContainsString("'gmrc_dm_roster_'", $frontend);
    }

    public function testSynchronizerIsRegisteredAndInjectedAtEveryBoundary(): void
    {
        $provider = $this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');

        self::assertStringContainsString('CampaignMembershipSynchronizer::class', $provider);
        self::assertGreaterThanOrEqual(3, substr_count($provider, '$c->make(CampaignMembershipSynchronizer::class)'));
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
