<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeGuildGateRegressionTest extends TestCase
{
    public function testNativeGuildGateUsesPkceWithoutCollectingWordPressPasswords(): void
    {
        $root = __DIR__ . '/../../../';
        $auth = file_get_contents($root . 'app/Mobile/PocketNativeAuth.php');
        $bridge = file_get_contents($root . 'app/Mobile/PocketNativeBridge.php');
        $gate = file_get_contents($root . 'app/Modules/GuildGate/Controllers/GuildGateController.php');
        $bootstrap = file_get_contents($root . 'great-marketrealm-companion.php');
        $environment = file_get_contents($root . 'native/pocket-companion/config/environments.json');

        self::assertStringContainsString("CALLBACK_URI = 'uk.co.greatmarketrealm.pocket://auth/callback'", $auth);
        self::assertStringContainsString("'/native/begin'", $auth);
        self::assertStringContainsString("'/native/token'", $auth);
        self::assertStringContainsString("'/native/revoke'", $auth);
        self::assertStringContainsString("'code_challenge_method' => 'S256'", $auth);
        self::assertStringContainsString('hash_equals', $auth);
        self::assertStringContainsString('delete_transient(self::codeKey($code))', $auth);
        self::assertStringContainsString("'/gmrc-pocket/v1/'", $auth);
        self::assertStringContainsString("'native' => 'system-browser-pkce-bearer'", $bridge);
        self::assertStringContainsString("'wordpress_password_in_native_app' => false", $bridge);
        self::assertStringContainsString('PocketNativeAuth::isNativeReturnRoute', $gate);
        self::assertStringContainsString('PocketNativeAuth::registerRoutes()', $bootstrap);
        self::assertStringContainsString('"wordpressPasswordInNativeApp": false', $environment);
        self::assertStringNotContainsString('password', file_get_contents($root . 'native/pocket-companion/www/index.html'));
    }
}
