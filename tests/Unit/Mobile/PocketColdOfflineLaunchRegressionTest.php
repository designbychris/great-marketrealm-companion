<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketColdOfflineLaunchRegressionTest extends TestCase
{
    public function testColdOfflineLaunchUsesAPrecachedPublicGuardPage(): void
    {
        $foundation = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketAppFoundation.php');

        self::assertStringContainsString("$asset === 'offline'", $foundation);
        self::assertStringContainsString("const CACHE='gmrc-pocket-static-v2'", $foundation);
        self::assertStringContainsString('const OFFLINE_URL=', $foundation);
        self::assertStringContainsString('caches.match(OFFLINE_URL)', $foundation);
        self::assertStringContainsString('The Guild is out of reach.', $foundation);
        self::assertStringContainsString('Your character data has not been cached.', $foundation);
        self::assertStringContainsString("fetch(event.request,{cache:'no-store'})", $foundation);
        self::assertStringNotContainsString('gmrc-pocket/v1/characters', $foundation);
    }
}
