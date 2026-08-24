<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class MarketPassFoundationRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testMarketPassRoutesCoverPlayerRedemptionAndDmLifecycle(): void
    {
        $routes = $this->source('app/Modules/DungeonMaster/Routes.php');
        self::assertStringContainsString("'/market-pass'", $routes);
        self::assertStringContainsString("'/dungeon-master/campaigns/{id}/market-pass'", $routes);
        self::assertStringContainsString("MarketPassController::class,'issue'", $routes);
        self::assertStringContainsString("MarketPassController::class,'revoke'", $routes);
    }

    public function testInviteCodesAreCryptographicallyRandomAndHumanFriendly(): void
    {
        $generator = $this->source('app/Core/Invitations/InviteCodeGenerator.php');
        self::assertStringContainsString('random_int(', $generator);
        self::assertStringContainsString('ABCDEFGHJKLMNPQRSTUVWXYZ23456789', $generator);
        self::assertStringContainsString("substr(\$characters, 0, 4) . '-'", $generator);
    }

    public function testMarketPassHasExpiryRevocationAndCaseInsensitiveNormalisation(): void
    {
        $model = $this->source('app/Modules/DungeonMaster/Models/MarketPass.php');
        self::assertStringContainsString('DEFAULT_LIFETIME = 604800', $model);
        self::assertStringContainsString('STATUS_REVOKED', $model);
        self::assertStringContainsString('isRedeemable', $model);
        self::assertStringContainsString('strtoupper', $model);
        self::assertStringContainsString("preg_replace('/[^A-Z0-9]/i'", $model);
    }

    public function testPassPersistenceIsCampaignScopedAndCollisionChecked(): void
    {
        $repository = $this->source('app/Modules/DungeonMaster/Repositories/MarketPassRepository.php');
        self::assertStringContainsString("_gmrc_market_pass_lookup", $repository);
        self::assertStringContainsString('GENERATION_ATTEMPTS = 12', $repository);
        self::assertStringContainsString('lookupExists(', $repository);
        self::assertStringContainsString('postIdForOwner(', $repository);
        self::assertStringContainsString('hash_equals(', $repository);
    }

    public function testRedeemingPassCreatesRosterMembershipWithoutCharacterAttachment(): void
    {
        $controller = $this->source('app/Modules/DungeonMaster/Controllers/MarketPassController.php');
        self::assertStringContainsString('campaignForCode(', $controller);
        self::assertStringContainsString('hasPlayer($campaign, $playerId)', $controller);
        self::assertStringContainsString('addPlayer($campaign, $playerId)', $controller);
        self::assertStringNotContainsString('attachCharacter(', $controller);
        self::assertStringContainsString('$campaign->ownerId() === $playerId', $controller);
        self::assertStringContainsString('$campaign->isArchived()', $controller);
    }

    public function testMarketPassCommandsHaveDedicatedNoncesAndPlayerBoundary(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        $request = $this->source('app/Modules/DungeonMaster/Requests/RedeemMarketPassRequest.php');
        self::assertStringContainsString('gmrc_market_pass_redeem', $frontend);
        self::assertStringContainsString('gmrc_dm_market_pass_', $frontend);
        self::assertStringContainsString('AccountType::PLAYER', $request);
        self::assertStringContainsString("user_can(\$userId, 'gmrc_access_companion')", $request);
    }

    public function testDmAndPlayerSurfacesExposeAccessibleMarketPassWorkflow(): void
    {
        $roster = $this->source('app/Modules/DungeonMaster/Views/players/index.php');
        $player = $this->source('app/Modules/DungeonMaster/Views/market-pass/index.php');
        $dashboard = $this->source('app/Modules/Dashboard/Views/index.php');
        $css = $this->source('assets/css/modules/dungeon-master/market-pass.css');
        self::assertStringContainsString('Issue Market Pass', $roster);
        self::assertStringContainsString('Rotate Market Pass', $roster);
        self::assertStringContainsString('Revoke Market Pass', $roster);
        self::assertStringContainsString('Redeem a Market Pass', $player);
        self::assertStringContainsString('Market Pass', $dashboard);
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
