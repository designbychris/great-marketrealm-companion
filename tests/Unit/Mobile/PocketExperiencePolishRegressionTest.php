<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketExperiencePolishRegressionTest extends TestCase
{
    public function testPolishIsScopedToOpenMobileCharacterAndPreservesViewport(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertIsString($source);
        self::assertStringContainsString('III.M.4C.4: presentation-only Pocket polish', $source);
        self::assertStringContainsString('.gmrc-pocket--character-open .gmrc-pocket-dashboard__viewport', $source);
        self::assertStringContainsString('.gmrc-pocket--character-open .gmrc-pocket-bottom-dock', $source);
        self::assertStringContainsString('viewport.append(back,hero,...panels.values())', $source);
        self::assertStringContainsString('bottomDock.append(dock,moreMenu,tabs)', $source);
        self::assertStringContainsString('@media(prefers-reduced-motion:reduce)', $source);
    }
}
