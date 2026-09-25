<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketDockLayoutRegressionTest extends TestCase
{
    public function testSharedDockAndMutuallyExclusiveExpandedPanels(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertStringContainsString("bottomDock.append(dock,moreMenu,tabs)", $source);
        self::assertStringContainsString("if(opening)diceTray.open=false", $source);
        self::assertStringContainsString("shell.append(viewport,bottomDock)", $source);
        self::assertStringContainsString(".gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock .gmrc-pocket-dice{max-height:min(60dvh,520px);overflow-y:auto", $source);
        self::assertStringContainsString(".gmrc-pocket-bottom-dock .gmrc-pocket-dice-dock .gmrc-pocket-dice:not([open]) .gmrc-pocket-dice__body{display:none}", $source);
    }
}
