<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class IlluminatedRegisterPresentationTest extends TestCase
{
    public function testRegisterCardConsumesPersistedPortraitViewModel(): void
    {
        $root = dirname(__DIR__, 5);
        $component = file_get_contents(
            $root . '/app/Views/components/entries/adventurer-entry.php'
        );
        $index = file_get_contents(
            $root . '/app/Modules/Characters/Views/index.php'
        );
        $controller = file_get_contents(
            $root . '/app/Modules/Characters/Controllers/CharacterController.php'
        );

        self::assertIsString($component);
        self::assertIsString($index);
        self::assertIsString($controller);
        self::assertStringContainsString('PortraitViewModel', $component);
        self::assertStringContainsString('$portraitModel->isCustom()', $component);
        self::assertStringContainsString('$portraitModel->svg()', $component);
        self::assertStringContainsString('$portraitModel?->attachmentUrl()', $component);
        self::assertStringContainsString('data-register-portrait', $component);
        self::assertStringContainsString('portrait-frame__generated', $component);
        self::assertStringContainsString(
            'gmrc-illuminated-portrait--has-race',
            $component
        );
        self::assertStringContainsString(
            'gmrc-illuminated-portrait--has-class',
            $component
        );
        self::assertStringContainsString(
            'gmrc-illuminated-portrait--complete',
            $component
        );
        self::assertStringContainsString('portrait-frame__image', $component);
        self::assertStringContainsString('portrait-frame__initials', $component);
        self::assertStringContainsString("'portrait' => \$characterPortrait", $index);
        self::assertStringContainsString('->forCharacters(', $controller);
    }

    public function testRegisterPortraitLinksToTheCharacterLedgerAndIsStyled(): void
    {
        $root = dirname(__DIR__, 5);
        $component = file_get_contents(
            $root . '/app/Views/components/entries/adventurer-entry.php'
        );
        $css = file_get_contents(
            $root . '/assets/css/guild-ledger.css'
        );

        self::assertIsString($component);
        self::assertIsString($css);
        self::assertStringContainsString('class="portrait-frame__link"', $component);
        self::assertStringContainsString('href="<?php echo esc_url($viewUrl); ?>"', $component);
        self::assertStringContainsString('Open %s’s Character Ledger', $component);
        self::assertStringContainsString('.portrait-frame--illuminated', $css);
        self::assertStringContainsString('.portrait-frame__generated', $css);
        self::assertStringContainsString('.portrait-frame__image', $css);
        self::assertStringContainsString('.portrait-frame__link:focus-visible', $css);
    }
}
