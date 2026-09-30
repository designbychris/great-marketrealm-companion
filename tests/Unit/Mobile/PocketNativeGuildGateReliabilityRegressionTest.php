<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Mobile;

use PHPUnit\Framework\TestCase;

final class PocketNativeGuildGateReliabilityRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 3);
    }

    public function testNativeGuildGatePagesAreExplicitlyProtectedFromPageCaching(): void
    {
        $auth = file_get_contents($this->root . '/app/Mobile/PocketNativeAuth.php');
        self::assertIsString($auth);
        self::assertStringContainsString("add_action('template_redirect', [self::class, 'protectNativeGuildGateFromCache'], 0)", $auth);
        self::assertStringContainsString("define('DONOTCACHEPAGE', true)", $auth);
        self::assertStringContainsString('nocache_headers();', $auth);
    }

    public function testNoCacheProtectionIsRestrictedToValidatedNativeReturnRoutes(): void
    {
        $auth = file_get_contents($this->root . '/app/Mobile/PocketNativeAuth.php');
        self::assertIsString($auth);
        self::assertStringContainsString('if (! self::isNativeReturnRoute($route))', $auth);
        self::assertStringContainsString("preg_match('#^native-auth/[a-f0-9]{48}$#'", $auth);
    }

    public function testStaleNativeGateNonceReturnsToFreshGateInsteadOfRawWordpress403(): void
    {
        $provider = file_get_contents($this->root . '/app/Providers/FrontendServiceProvider.php');
        self::assertIsString($provider);
        self::assertStringContainsString('PocketNativeAuth::isNativeReturnRoute($returnRoute)', $provider);
        self::assertStringContainsString("'gmrc_native_retry' => wp_generate_password(12, false, false)", $provider);
        self::assertStringContainsString("'return_route' => \$returnRoute", $provider);
        self::assertStringContainsString('wp_safe_redirect($freshGate);', $provider);
    }

    public function testOrdinaryCompanionNonceFailuresStillKeepExistingSecurityBoundary(): void
    {
        $provider = file_get_contents($this->root . '/app/Providers/FrontendServiceProvider.php');
        self::assertIsString($provider);
        self::assertStringContainsString("'The form request could not be verified.'", $provider);
        self::assertStringContainsString("'response' => 403", $provider);
        self::assertStringContainsString('$nativeGuildGate = $publicGuildGateRoute', $provider);
    }
}
