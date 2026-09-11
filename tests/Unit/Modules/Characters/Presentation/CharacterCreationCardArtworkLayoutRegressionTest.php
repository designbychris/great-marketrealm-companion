<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Presentation;

use PHPUnit\Framework\TestCase;

final class CharacterCreationCardArtworkLayoutRegressionTest extends TestCase
{
    private function root(string $path): string
    {
        return dirname(__DIR__, 5) . '/' . ltrim($path, '/');
    }

    public function test_illustrated_choice_cards_are_taller_and_edge_to_edge(): void
    {
        $css = (string) file_get_contents($this->root('assets/css/modules/characters/choice-selector.css'));

        self::assertStringContainsString('.gmrc-choice-card__image.gmrc-choice-card__image--illustrated', $css);
        self::assertStringContainsString('height: 15.5rem;', $css);
        self::assertStringContainsString('padding: 0 !important;', $css);
        self::assertStringContainsString('max-width: none;', $css);
    }

    public function test_background_artwork_uses_the_same_edge_to_edge_treatment(): void
    {
        $css = (string) file_get_contents($this->root('assets/css/modules/characters/background-selector.css'));

        self::assertStringContainsString('.gmrc-background-option__image.gmrc-background-option__image--illustrated', $css);
        self::assertStringContainsString('height: 15.5rem;', $css);
        self::assertStringContainsString('padding: 0 !important;', $css);
    }

    public function test_steward_preview_is_a_compact_media_frame(): void
    {
        $css = (string) file_get_contents($this->root('assets/css/admin.css'));

        self::assertStringContainsString('.gmrc-character-card-artwork-admin .gmrc-card-artwork-admin__preview-frame', $css);
        self::assertStringContainsString('max-width: 300px;', $css);
        self::assertStringContainsString('clip-path: none;', $css);
        self::assertStringContainsString('min-height: 0;', $css);
    }

    public function test_steward_editor_remains_responsive(): void
    {
        $css = (string) file_get_contents($this->root('assets/css/admin.css'));

        self::assertStringContainsString('@media (max-width: 960px)', $css);
        self::assertStringContainsString('.gmrc-character-card-artwork-admin .gmrc-card-artwork-admin__editor-artwork', $css);
    }
}
