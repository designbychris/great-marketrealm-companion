<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketAppViewportRegressionTest extends TestCase
{
    public function testDedicatedCharacterViewportAndSharedBottomDock(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertStringContainsString("const viewport=el('div','gmrc-pocket-dashboard__viewport')", $source);
        self::assertStringContainsString('viewport.append(back,hero,...panels.values())', $source);
        self::assertStringContainsString('shell.append(viewport,bottomDock)', $source);
        self::assertStringContainsString('.gmrc-pocket.gmrc-pocket--character-open{position:fixed!important', $source);
        self::assertStringContainsString('.gmrc-pocket--character-open .gmrc-pocket-dashboard__viewport{flex:1 1 auto!important', $source);
        self::assertStringContainsString('.gmrc-pocket--character-open .gmrc-pocket-bottom-dock{position:relative!important', $source);
        self::assertStringContainsString('.gmrc-pocket--character-open .gmrc-pocket-bottom-dock .gmrc-pocket-dice__body{min-height:0!important;overflow-y:auto!important', $source);
    }
}
