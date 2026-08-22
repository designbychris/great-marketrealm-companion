<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use PHPUnit\Framework\TestCase;

final class GuildLibraryPolishRegressionTest extends TestCase
{
    public function testApprovedArtworkMappingRemainsOnContentWorkspace(): void
    {
        $css = $this->css();

        self::assertStringContainsString(
            '.gmrc-content:has(> .gmrc-guild-library)',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-content:has(> .gmrc-background-register)',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-content:has(> .gmrc-armoury)',
            $css
        );
        self::assertStringContainsString(
            'guild-library-background.png',
            $css
        );
        self::assertStringContainsString(
            'guild-library-auby.png',
            $css
        );
        self::assertStringContainsString(
            'guild-library-sage.png',
            $css
        );
        self::assertStringNotContainsString(
            'guild-library-armoury.png',
            $css
        );
    }

    public function testArtworkNeverMovesBackOntoApplicationNavigationContainer(): void
    {
        $css = $this->css();

        self::assertStringNotContainsString('.gmrc-app-main:has(', $css);
        self::assertStringNotContainsString('.gmrc-guild-library::before', $css);
        self::assertStringNotContainsString('.gmrc-relics::before', $css);
    }

    public function testOnlyTheCanonicalLibraryStylesheetIsEnqueued(): void
    {
        $provider = file_get_contents(
            dirname(__DIR__, 4)
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString('guild-library.css', $provider);
        self::assertStringNotContainsString(
            'library-visual-treatments.css',
            $provider
        );
        self::assertStringNotContainsString(
            'armoury-background-treatment.css',
            $provider
        );
    }

    public function testRelicsHeroDoesNotRepeatAubyArtwork(): void
    {
        $css = $this->css();
        $start = strpos($css, '.gmrc-relics__hero {');

        self::assertIsInt($start);

        $end = strpos($css, '}', $start);
        self::assertIsInt($end);

        $hero = substr($css, $start, $end - $start);

        self::assertStringContainsString('background: transparent', $hero);
        self::assertStringNotContainsString('guild-library-auby.png', $hero);
    }

    public function testFinalPolishRetainsAccessibilityFallbacks(): void
    {
        $css = $this->css();

        self::assertStringContainsString(':focus-visible', $css);
        self::assertStringContainsString(
            '@media (prefers-reduced-transparency: reduce)',
            $css
        );
        self::assertStringContainsString('@media (forced-colors: active)', $css);
        self::assertStringContainsString('color: LinkText;', $css);
    }

    private function css(): string
    {
        $css = file_get_contents(
            dirname(__DIR__, 4)
            . '/assets/css/modules/library/guild-library.css'
        );

        self::assertIsString($css);

        return $css;
    }
}
