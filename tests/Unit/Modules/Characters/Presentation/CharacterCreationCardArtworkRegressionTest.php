<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Presentation;

use PHPUnit\Framework\TestCase;

final class CharacterCreationCardArtworkRegressionTest extends TestCase
{
    private function root(string $path): string
    {
        return dirname(__DIR__, 5) . '/' . ltrim($path, '/');
    }

    public function test_character_creator_receives_steward_card_artwork_map(): void
    {
        $controller = (string) file_get_contents($this->root('app/Modules/Characters/Controllers/CharacterController.php'));
        self::assertStringContainsString("'cardArtwork' =>", $controller);
        self::assertStringContainsString('CharacterCardArtworkRegister())->presentationMap()', $controller);
    }

    public function test_race_cards_render_artwork_with_monogram_fallback(): void
    {
        $view = (string) file_get_contents($this->root('app/Modules/Characters/Views/create.php'));
        self::assertStringContainsString("\$cardArtwork['race'][\$identifier]", $view);
        self::assertStringContainsString('gmrc-choice-card__artwork', $view);
        self::assertStringContainsString('gmrc-choice-card__monogram', $view);
    }

    public function test_class_cards_render_artwork_with_monogram_fallback(): void
    {
        $view = (string) file_get_contents($this->root('app/Modules/Characters/Views/create.php'));
        self::assertStringContainsString("\$cardArtwork['class'][\$identifier]", $view);
        self::assertStringContainsString('$classArtworkUrl', $view);
    }

    public function test_background_cards_render_artwork_with_monogram_fallback(): void
    {
        $view = (string) file_get_contents($this->root('app/Modules/Characters/Views/create.php'));
        self::assertStringContainsString("\$cardArtwork['background'][\$identifier]", $view);
        self::assertStringContainsString('gmrc-background-option__artwork', $view);
    }

    public function test_choice_card_artwork_is_taller_and_gently_zooms(): void
    {
        $css = (string) file_get_contents($this->root('assets/css/modules/characters/choice-selector.css'));
        self::assertStringContainsString('min-height: 14rem;', $css);
        self::assertStringContainsString('transform: scale(1.055);', $css);
        self::assertStringContainsString('object-fit: cover;', $css);
    }

    public function test_background_artwork_has_the_same_visual_treatment(): void
    {
        $css = (string) file_get_contents($this->root('assets/css/modules/characters/background-selector.css'));
        self::assertStringContainsString('.gmrc-background-option__artwork', $css);
        self::assertStringContainsString('transform: scale(1.055);', $css);
        self::assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
    }

    public function test_stewards_office_exposes_character_card_artwork_management(): void
    {
        $provider = (string) file_get_contents($this->root('app/Providers/AdministrationServiceProvider.php'));
        $office = (string) file_get_contents($this->root('app/Modules/Administration/Views/stewards-office.php'));
        self::assertStringContainsString("section === 'character-card-artwork'", $provider);
        self::assertStringContainsString('gmrc_save_character_card_artwork', $provider);
        self::assertStringContainsString('Character Card Artwork', $office);
    }

    public function test_admin_artwork_picker_uses_wordpress_media_library(): void
    {
        $view = (string) file_get_contents($this->root('app/Modules/Administration/Views/character-card-artwork.php'));
        $js = (string) file_get_contents($this->root('assets/js/admin/character-card-artwork.js'));
        self::assertStringContainsString('data-gmrc-card-artwork-select', $view);
        self::assertStringContainsString('wp.media({', $js);
        self::assertStringContainsString("library: { type: 'image' }", $js);
    }
}
