<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Invitations;

use PHPUnit\Framework\TestCase;

final class FellowshipSealsRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 5);
    }

    public function testFellowshipSealRoutesCoverCustodianAndPlayerLifecycle(): void
    {
        $routes = $this->source('app/Modules/Parties/Routes.php');

        self::assertStringContainsString("'/fellowship-seal'", $routes);
        self::assertStringContainsString("'/parties/{id}/seal'", $routes);
        self::assertStringContainsString("FellowshipSealController::class, 'redeem'", $routes);
        self::assertStringContainsString("FellowshipSealController::class, 'issue'", $routes);
        self::assertStringContainsString("FellowshipSealController::class, 'revoke'", $routes);
    }

    public function testSealReusesSecureCoreInviteCodeGenerator(): void
    {
        $provider = $this->source('app/Modules/Parties/PartiesServiceProvider.php');
        $generator = $this->source('app/Core/Invitations/InviteCodeGenerator.php');
        $repository = $this->source('app/Modules/Parties/Repositories/FellowshipSealRepository.php');

        self::assertStringContainsString('InviteCodeGenerator::class', $provider);
        self::assertStringContainsString('random_int(', $generator);
        self::assertStringContainsString('private InviteCodeGenerator $codes', $repository);
        self::assertStringContainsString('GENERATION_ATTEMPTS = 12', $repository);
        self::assertStringContainsString('lookupExists(', $repository);
    }

    public function testSealHasExpiryRevocationAndHumanFriendlyNormalisation(): void
    {
        $model = $this->source('app/Modules/Parties/Models/FellowshipSeal.php');

        self::assertStringContainsString('DEFAULT_LIFETIME = 604800', $model);
        self::assertStringContainsString('STATUS_REVOKED', $model);
        self::assertStringContainsString('isRedeemable', $model);
        self::assertStringContainsString('strtoupper', $model);
        self::assertStringContainsString("preg_replace('/[^A-Z0-9]/i'", $model);
    }

    public function testSealLookupIsCollisionCheckedAndRejectsAmbiguousRecords(): void
    {
        $repository = $this->source('app/Modules/Parties/Repositories/FellowshipSealRepository.php');

        self::assertStringContainsString("'_gmrc_fellowship_seal_lookup'", $repository);
        self::assertStringContainsString('count($posts) !== 1', $repository);
        self::assertStringContainsString('hash_equals(', $repository);
        self::assertStringContainsString('$seal->isRedeemable()', $repository);
    }

    public function testRedemptionPreservesCharacterOwnershipAndCustodianBoundary(): void
    {
        $action = $this->source('app/Modules/Parties/Actions/RedeemFellowshipSealAction.php');

        self::assertStringContainsString('findForOwner(', $action);
        self::assertStringContainsString('$characterId,', $action);
        self::assertStringContainsString('$accountId', $action);
        self::assertStringContainsString('$party->ownerId()->value() === $accountId', $action);
        self::assertStringContainsString('PartyMembershipRole::member()', $action);
        self::assertStringNotContainsString('PartyMembershipRole::leader()', $action);
    }

    public function testRedemptionIsIdempotentForExistingFellowshipMember(): void
    {
        $action = $this->source('app/Modules/Parties/Actions/RedeemFellowshipSealAction.php');

        self::assertStringContainsString('if ($party->hasMember($characterId))', $action);
        self::assertStringContainsString('return $party;', $action);
        self::assertStringContainsString('$party->addMember(', $action);
        self::assertStringContainsString('$this->parties->save($party)', $action);
    }

    public function testOnlyRegisteredPlayersMayRedeemASeal(): void
    {
        $request = $this->source('app/Modules/Parties/Requests/RedeemFellowshipSealRequest.php');
        $controller = $this->source('app/Modules/Parties/Controllers/FellowshipSealController.php');

        self::assertStringContainsString('AccountType::PLAYER', $request);
        self::assertStringContainsString("user_can(\$userId, 'gmrc_access_companion')", $request);
        self::assertStringContainsString('GuildProfile::accountType($accountId) !== AccountType::PLAYER', $controller);
        self::assertStringContainsString('status_header(403)', $controller);
    }

    public function testApplicationGatewayUsesDedicatedSealNonces(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');

        self::assertStringContainsString("\$route === 'fellowship-seal'", $frontend);
        self::assertStringContainsString("'gmrc_fellowship_seal_redeem'", $frontend);
        self::assertStringContainsString("'#^parties/([^/]+)/seal$#'", $frontend);
        self::assertStringContainsString("'gmrc_fellowship_seal_'", $frontend);
    }

    public function testFellowshipSurfacesExposeIssueAndRedeemWithoutManagementTransfer(): void
    {
        $register = $this->source('app/Modules/Parties/Views/index.php');
        $show = $this->source('app/Modules/Parties/Views/show.php');
        $redeem = $this->source('app/Modules/Parties/Views/fellowship-seal.php');
        $manage = $this->source('app/Modules/Parties/Views/fellowship-seal-manage.php');

        self::assertStringContainsString('Redeem a Fellowship Seal', $register);
        self::assertStringContainsString('Fellowship Seal', $show);
        self::assertStringContainsString('Join Fellowship', $redeem);
        self::assertStringContainsString('Issue Fellowship Seal', $manage);
        self::assertStringContainsString('Custodianship and Company administration remain with you.', $manage);
    }

    public function testSealPresentationCarriesResponsiveAndAccessibilitySafeguards(): void
    {
        $css = $this->source('assets/css/modules/parties/fellowship-seals.css');

        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('@media (max-width: 720px)', $css);
        self::assertStringContainsString('prefers-reduced-motion: reduce', $css);
        self::assertStringContainsString('prefers-reduced-transparency: reduce', $css);
        self::assertStringContainsString('forced-colors: active', $css);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);

        return $source;
    }
}
