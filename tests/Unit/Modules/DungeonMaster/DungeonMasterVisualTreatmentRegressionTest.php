<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class DungeonMasterVisualTreatmentRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testDedicatedDungeonMasterArtworkExists(): void
    {
        $path = $this->root
            . '/assets/images/dungeon-master/dungeon-master-desk-background.png';

        self::assertFileExists($path);
        self::assertGreaterThan(500000, filesize($path));
    }

    public function testArtworkIsScopedToContentWorkspaceNotApplicationShell(): void
    {
        $css = $this->source(
            'assets/css/modules/dungeon-master/dungeon-master-desk.css'
        );

        self::assertStringContainsString(
            '.gmrc-content:has(> .gmrc-dm-desk)',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-content:has(> .gmrc-campaign-register)',
            $css
        );
        self::assertStringNotContainsString('.gmrc-app-main:has(', $css);
        self::assertStringContainsString(
            'dungeon-master-desk-background.png',
            $css
        );
    }

    public function testDeskUsesArtworkFirstHeroAndFourWorkspaceLedgers(): void
    {
        $view = $this->source(
            'app/Modules/DungeonMaster/Views/index.php'
        );

        self::assertStringContainsString(
            'Plan adventures. Guide legends. Shape the Marketrealm.',
            $view
        );
        self::assertStringContainsString('gmrc-dm-desk__hero-copy', $view);
        self::assertStringContainsString('gmrc-dm-ledger--campaign', $view);
        self::assertStringContainsString('gmrc-dm-ledger--session', $view);
        self::assertStringContainsString('gmrc-dm-ledger--encounter', $view);
        self::assertStringContainsString('gmrc-dm-ledger--roster', $view);
    }

    public function testCampaignRegisterSharesImmersiveDmTreatment(): void
    {
        $css = $this->source(
            'assets/css/modules/dungeon-master/campaign-register.css'
        );

        self::assertStringContainsString('rgba(21, 13, 15, .88)', $css);
        self::assertStringContainsString('backdrop-filter: blur(7px)', $css);
        self::assertStringContainsString('@media (forced-colors: active)', $css);
        self::assertStringContainsString(
            '@media (prefers-reduced-transparency: reduce)',
            $css
        );
    }

    public function testVisualTreatmentHasMobileAndAccessibilityFallbacks(): void
    {
        $css = $this->source(
            'assets/css/modules/dungeon-master/dungeon-master-desk.css'
        );

        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString('@media (max-width: 700px)', $css);
        self::assertStringContainsString('background-attachment: scroll', $css);
        self::assertStringContainsString(
            '@supports not (backdrop-filter: blur(1px))',
            $css
        );
        self::assertStringContainsString(
            '@media (prefers-reduced-transparency: reduce)',
            $css
        );
        self::assertStringContainsString('@media (forced-colors: active)', $css);
    }

    public function testPhaseDocumentationRecordsVisualTreatment(): void
    {
        $docs = $this->source(
            'docs/GuildArchives/Development/DungeonMasterPhase315.md'
        );

        self::assertStringContainsString(
            "III.15.1A — Dungeon Master's Desk Visual Treatment",
            $docs
        );
        self::assertStringContainsString('3,403 tests', $docs);
        self::assertStringContainsString('11,216 assertions', $docs);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
