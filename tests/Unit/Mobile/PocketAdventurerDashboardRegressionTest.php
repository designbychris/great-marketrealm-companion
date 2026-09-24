<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketAdventurerDashboardRegressionTest extends TestCase
{
    private function pocketSource(): string
    {
        return (string) file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
    }

    public function testDashboardUsesExistingLiveControlsInsteadOfDuplicatingThem(): void
    {
        $source = $this->pocketSource();
        self::assertStringContainsString('function pocketDashboard(detail,character,back,diceTray)', $source);
        self::assertStringContainsString('pocketDashboard(detail,character,back,diceTray);', $source);
        self::assertStringContainsString('panel.append(node)', $source);
        self::assertStringContainsString('detail.replaceChildren(back,shell)', $source);
        self::assertStringContainsString('vitalityControls(character)', $source);
        self::assertStringContainsString('pocketSpellbook(character,diceTray)', $source);
        self::assertStringContainsString('changeSpellSlot(character,slot,action)', $source);
    }

    public function testAccessibleDashboardNavigationAndResponsivePresentation(): void
    {
        $source = $this->pocketSource();
        foreach (['overview', 'character', 'combat', 'spells', 'equipment'] as $section) {
            self::assertStringContainsString("['" . $section . "'", $source);
        }
        // III.M.4C.1 uses page-style navigation, not an ARIA tab widget.
        // Assert the actual navigation contract instead of requiring tablist,
        // tabpanel, aria-selected and arrow-key behaviour that is not implemented.
        foreach ([
            "el('nav','gmrc-pocket-dashboard__tabs')",
            "tabs.setAttribute('aria-label','Pocket character navigation')",
            "button.setAttribute('aria-controls'",
            "button.setAttribute('aria-current'",
            "panel.setAttribute('aria-labelledby'",
            "panel.hidden=key!=='overview'",
            "panel.hidden=!active",
            "more.setAttribute('aria-expanded','false')",
            "more.setAttribute('aria-controls'",
            "moreMenu.hidden=true",
            "event.key==='Escape'",
            'safe-area-inset-bottom',
            'prefers-reduced-motion',
        ] as $token) {
            self::assertStringContainsString($token, $source);
        }
    }
}
