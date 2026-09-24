<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketPersistentDiceworksRegressionTest extends TestCase
{
    public function testOneSharedDiceTrayIsDockedOutsideSwitchingPanels(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertStringContainsString("const dock=el('aside','gmrc-pocket-dice-dock')", $source);
        self::assertStringContainsString('dock.append(diceTray)', $source);
        self::assertStringContainsString('shell.append(hero,tabs,moreMenu,...panels.values(),dock)', $source);
        self::assertStringContainsString("moreMenu.hidden=true", $source);
        self::assertStringNotContainsString("['dice','Diceworks',[diceTray]]", $source);
        self::assertStringNotContainsString("activate('dice')", $source);
    }

    public function testCompactResultAndSafeAreaPresentation(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertStringContainsString("latest.textContent=descriptor+' · '+total", $source);
        self::assertStringContainsString("tray.rollCheck=(label,mod)=>{tray.open=true", $source);
        self::assertStringContainsString('gmrc-pocket-dice-dock{position:fixed', $source);
        self::assertStringContainsString('safe-area-inset-bottom', $source);
        self::assertStringContainsString('prefers-reduced-motion', $source);
    }
}
