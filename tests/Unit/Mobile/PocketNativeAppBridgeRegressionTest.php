<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeAppBridgeRegressionTest extends TestCase
{
    public function testNativeBridgeFormalisesExistingPocketBoundaryWithoutOfflineCharacterStorage(): void
    {
        $bridge = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketNativeBridge.php');
        $api = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketApi.php');
        $page = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');

        self::assertStringContainsString("CONTRACT_VERSION = '1.1'", $bridge);
        self::assertStringContainsString("'browser' => 'wordpress-cookie-rest-nonce'", $bridge);
        self::assertStringContainsString("'same_origin_required' => false", $bridge);
        self::assertStringContainsString("'offline_character_writes' => false", $bridge);
        self::assertStringContainsString("'character_cache' => 'none'", $bridge);
        self::assertStringContainsString("'/bridge'", $api);
        self::assertStringContainsString('PocketNativeBridge::contract()', $api);
        self::assertStringContainsString("'Cache-Control' => 'private, no-store'", $api);
        self::assertStringContainsString("'bridge' => esc_url_raw(rest_url('gmrc-pocket/v1/bridge'))", $page);
    }
}
