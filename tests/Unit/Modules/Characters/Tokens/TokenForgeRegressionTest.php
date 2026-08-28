<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Tokens;

use PHPUnit\Framework\TestCase;

final class TokenForgeRegressionTest extends TestCase
{
    public function testTokenPersistenceIsSeparateFromCharacterPortraitPersistence(): void
    {
        $repository = $this->read(
            'app/Modules/Characters/Tokens/Repositories/CharacterTokenRepository.php'
        );
        $portraitRepository = $this->read(
            'app/Modules/Characters/Portraits/Repositories/CharacterPortraitRepository.php'
        );

        self::assertStringContainsString("'_gmrc_tabletop_token'", $repository);
        self::assertStringNotContainsString("'_gmrc_portrait_mode'", $repository);
        self::assertStringNotContainsString("'_gmrc_tabletop_token'", $portraitRepository);
    }

    public function testTokenForgeSupportsPortraitFallbackAndDedicatedUploads(): void
    {
        $model = $this->read(
            'app/Modules/Characters/Tokens/Models/CharacterToken.php'
        );
        $presenter = $this->read(
            'app/Modules/Characters/Tokens/Services/CharacterTokenPresenter.php'
        );

        self::assertStringContainsString("SOURCE_PORTRAIT = 'portrait'", $model);
        self::assertStringContainsString("SOURCE_CUSTOM = 'custom'", $model);
        self::assertStringContainsString('withCustomAttachment', $model);
        self::assertStringContainsString('usePortrait', $model);
        self::assertStringContainsString('Deleted/unavailable custom media gracefully falls back', $presenter);
    }

    public function testTokenForgeUsesNonDestructiveCropRecipe(): void
    {
        $model = $this->read(
            'app/Modules/Characters/Tokens/Models/CharacterToken.php'
        );
        $component = $this->read(
            'app/Views/components/media/tabletop-token-forge.php'
        );

        foreach (['focus_x', 'focus_y', 'zoom', 'frame'] as $field) {
            self::assertStringContainsString("'{$field}'", $model);
        }

        self::assertStringContainsString('name="token_focus_x"', $component);
        self::assertStringContainsString('name="token_focus_y"', $component);
        self::assertStringContainsString('name="token_zoom"', $component);
        self::assertStringContainsString('never cropped or overwritten', $component);
    }

    public function testTokenMutationUsesOwnedCharacterAndDedicatedNonceBoundary(): void
    {
        $controller = $this->read(
            'app/Modules/Characters/Controllers/CharacterController.php'
        );
        $provider = $this->read('app/Providers/FrontendServiceProvider.php');
        $routes = $this->read('app/Modules/Characters/Routes.php');

        self::assertStringContainsString("'/characters/{id}/tabletop-token'", $routes);
        self::assertStringContainsString('saveTabletopToken', $routes);
        self::assertStringContainsString('resetTabletopToken', $routes);
        self::assertStringContainsString('$character = $this->findCharacter($id);', $controller);
        self::assertStringContainsString("gmrc_character_tabletop_token_", $provider);
    }

    public function testTokenUploadAcceptsOnlySafeWebImagesWithinFourMegabytes(): void
    {
        $controller = $this->read(
            'app/Modules/Characters/Controllers/CharacterController.php'
        );

        foreach (['image/jpeg', 'image/png', 'image/webp'] as $mime) {
            self::assertStringContainsString("'{$mime}'", $controller);
        }

        self::assertStringContainsString('4 * MB_IN_BYTES', $controller);
        self::assertStringContainsString("media_handle_upload('gmrc_tabletop_token', 0)", $controller);
    }

    public function testLedgerPresentsInteractiveTokenForgeWithoutChangingPortrait(): void
    {
        $view = $this->read('app/Modules/Characters/Views/show.php');
        $component = $this->read(
            'app/Views/components/media/tabletop-token-forge.php'
        );
        $script = $this->read(
            'assets/js/components/media/tabletop-token-forge.js'
        );

        self::assertStringContainsString("'components.media.tabletop-token-forge'", $view);
        self::assertStringContainsString('Adventurer’s Token Forge', $component);
        self::assertStringContainsString('Upload a dedicated token', $component);
        self::assertStringContainsString("style.setProperty('--gmrc-token-focus-x'", $script);
        self::assertStringContainsString("style.setProperty('--gmrc-token-zoom'", $script);
    }

    private function read(string $relative): string
    {
        $contents = file_get_contents(dirname(__DIR__, 5) . '/' . $relative);
        self::assertIsString($contents);
        return $contents;
    }
}
