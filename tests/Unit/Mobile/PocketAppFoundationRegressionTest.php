<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketAppFoundationRegressionTest extends TestCase
{
    public function testInstallablePocketFoundationIsRegisteredWithoutCachingPrivateCharacterData(): void
    {
        $foundation = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketAppFoundation.php');
        $page = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');

        self::assertStringContainsString("rel=\"manifest\"", $foundation);
        self::assertStringContainsString("'display' => 'standalone'", $foundation);
        self::assertStringContainsString("'theme_color' => '#192d22'", $foundation);
        self::assertStringContainsString('Service-Worker-Allowed: /', $foundation);
        self::assertStringContainsString("if(event.request.method!=='GET')return", $foundation);
        self::assertStringNotContainsString('gmrc-pocket/v1/characters', $foundation);
        self::assertStringContainsString("navigator.serviceWorker.register(config.serviceWorker,{scope:'/'})", $page);
    }

    public function testRequiredPocketApplicationIconsExist(): void
    {
        self::assertFileExists(__DIR__ . '/../../../assets/images/pocket/app-icon-192.png');
        self::assertFileExists(__DIR__ . '/../../../assets/images/pocket/app-icon-512.png');
    }
}
