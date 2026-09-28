<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketConnectionGuardRegressionTest extends TestCase
{
    public function testPocketConnectionGuardPausesLiveWritesWithoutCachingPrivateData(): void
    {
        $page = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        $foundation = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketAppFoundation.php');

        self::assertStringContainsString('gmrc-pocket-connection', $page);
        self::assertStringContainsString("window.addEventListener('offline'", $page);
        self::assertStringContainsString("window.addEventListener('online'", $page);
        self::assertStringContainsString('data-pocket-network-write', $page);
        self::assertStringContainsString('You are offline. HP was not changed.', $page);
        self::assertStringContainsString('You are offline. Spell slots were not changed.', $page);
        self::assertStringContainsString("event.request.mode==='navigate'", $foundation);
        self::assertStringContainsString('Your character data has not been cached.', $foundation);
        self::assertStringContainsString("'Cache-Control':'no-store'", $foundation);
        self::assertStringNotContainsString('gmrc-pocket/v1/characters', $foundation);
    }
}
