<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate\Certification;

use PHPUnit\Framework\TestCase;

final class GuildAccountMembershipCertificationTest extends TestCase
{
    public function testProfileReceivesCanonicalMembershipSummary(): void
    {
        $controller = $this->source('app/Modules/GuildGate/Controllers/GuildGateController.php');
        $provider = $this->source('app/Modules/GuildGate/GuildGateServiceProvider.php');

        self::assertStringContainsString('GuildMembershipSummary $memberships', $controller);
        self::assertStringContainsString("'membershipSummary' => \$this->memberships->forAccount(", $controller);
        self::assertStringContainsString('GuildMembershipSummary::class', $provider);
    }

    public function testMembershipSummaryKeepsDmAndPlayerCampaignSemanticsSeparate(): void
    {
        $service = $this->source('app/Modules/GuildGate/Services/GuildMembershipSummary.php');

        self::assertStringContainsString('$accountType === AccountType::DM', $service);
        self::assertStringContainsString('$this->campaigns->allForOwner($accountId)', $service);
        self::assertStringContainsString('$this->playerCampaigns->allForPlayer($accountId)', $service);
        self::assertStringContainsString("'Campaigns stewarded'", $service);
        self::assertStringContainsString("'Campaign memberships'", $service);
    }

    public function testMembershipSummarySeparatesOwnedAndSharedFellowships(): void
    {
        $service = $this->source('app/Modules/GuildGate/Services/GuildMembershipSummary.php');

        self::assertStringContainsString('$this->fellowships->allForAccount($accountId)', $service);
        self::assertStringContainsString("'owned_fellowships'", $service);
        self::assertStringContainsString("'shared_fellowships'", $service);
        self::assertStringContainsString('$fellowship->ownerId()->value() === $accountId', $service);
    }

    public function testProfileMembershipCertificateLinksToLiveRegisters(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/profile.php');

        self::assertStringContainsString('Certified relationships', $view);
        self::assertStringContainsString("'characters'", $view);
        self::assertStringContainsString("'active-campaigns'", $view);
        self::assertStringContainsString("'dungeon-master/campaigns'", $view);
        self::assertStringContainsString("'parties'", $view);
    }

    public function testCharacterDeletionChecksRelationshipsBeforeDeletingAnything(): void
    {
        $action = $this->source('app/Modules/Characters/Actions/DeleteCharacterAction.php');
        $guard = strpos($action, '$this->memberships->assertDeletable($id);');
        $portrait = strpos($action, '$this->portraits->delete($id);');
        $delete = strpos($action, '$this->characters->delete(');

        self::assertIsInt($guard);
        self::assertIsInt($portrait);
        self::assertIsInt($delete);
        self::assertLessThan($portrait, $guard);
        self::assertLessThan($delete, $guard);
    }

    public function testLiveCampaignMembershipBlocksCharacterDeletion(): void
    {
        $guard = $this->source('app/Modules/Characters/Services/CharacterMembershipGuard.php');

        self::assertStringContainsString('$this->campaigns->all()', $guard);
        self::assertStringContainsString('$this->rosters->members($campaign)', $guard);
        self::assertStringContainsString('in_array($needle, $member[\'character_ids\'], true)', $guard);
        self::assertStringContainsString('$campaign->isArchived()', $guard);
    }

    public function testFellowshipMembershipBlocksCharacterDeletionAcrossAccounts(): void
    {
        $guard = $this->source('app/Modules/Characters/Services/CharacterMembershipGuard.php');

        self::assertStringContainsString('$this->parties->allAcrossOwners()', $guard);
        self::assertStringContainsString('$party->memberships()', $guard);
        self::assertStringContainsString('$membership->characterId()->value() === $needle', $guard);
    }

    public function testBlockedDeletionReturnsARecoverableGuildError(): void
    {
        $guard = $this->source('app/Modules/Characters/Services/CharacterMembershipGuard.php');
        $controller = $this->source('app/Modules/Characters/Controllers/CharacterController.php');

        self::assertStringContainsString('Release them from those active Campaigns or Fellowships', $guard);
        self::assertStringContainsString('catch (RuntimeException $exception)', $controller);
        self::assertStringContainsString('$this->flash->error($exception->getMessage())', $controller);
        self::assertStringContainsString('$this->charactersUrl()', $controller);
    }

    public function testMembershipCertificationDoesNotReopenGuildRoleMutation(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/profile.php');
        $update = $this->source('app/Modules/GuildGate/Services/UpdateGuildProfile.php');

        self::assertStringContainsString('cannot be changed from this profile form', $view);
        self::assertStringNotContainsString('account_type', $update);
        self::assertStringNotContainsString('set_role(', $update);
    }

    public function testMembershipCertificateKeepsResponsiveAndAccessibilityCoverage(): void
    {
        $css = $this->source('assets/css/modules/guild-gate/guild-profile.css');

        self::assertStringContainsString('.gmrc-guild-profile__memberships', $css);
        self::assertStringContainsString('.gmrc-guild-profile__membership-grid', $css);
        self::assertStringContainsString('@media(max-width:760px)', $css);
        self::assertStringContainsString('@media(prefers-reduced-transparency:reduce)', $css);
        self::assertStringContainsString('@media(forced-colors:active)', $css);
        self::assertStringContainsString(':focus-visible', $css);
    }

    public function testPhaseDoesNotAddACompetingPostGateway(): void
    {
        $routes = $this->source('app/Modules/GuildGate/Routes.php');
        $profile = $this->source('app/Modules/GuildGate/Views/profile.php');

        self::assertStringContainsString("'/guild-profile'", $routes);
        self::assertStringContainsString('name="action" value="gmrc_app_request"', $profile);
        self::assertStringContainsString('name="gmrc_route" value="guild-profile"', $profile);
        self::assertStringNotContainsString('admin_post_gmrc_guild_membership', $routes);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 5) . '/' . $path);
        self::assertIsString($source);

        return $source;
    }
}
