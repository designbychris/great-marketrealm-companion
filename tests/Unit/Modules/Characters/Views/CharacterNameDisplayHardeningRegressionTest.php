<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class CharacterNameDisplayHardeningRegressionTest extends TestCase
{
    public function test_character_name_inputs_expose_the_domain_maximum_to_the_browser(): void
    {
        $create = (string) file_get_contents(dirname(__DIR__, 5) . '/app/Modules/Characters/Views/create.php');
        $edit = (string) file_get_contents(dirname(__DIR__, 5) . '/app/Modules/Characters/Views/edit.php');
        $component = (string) file_get_contents(dirname(__DIR__, 5) . '/app/Views/components/controls/scribe-input.php');

        self::assertStringContainsString("'maxlength' => '80'", $create);
        self::assertStringContainsString("'maxlength' => '80'", $edit);
        self::assertStringContainsString('maxlength="<?php echo esc_attr($maxlength); ?>"', $component);
    }

    public function test_open_ledger_names_shrink_and_wrap_before_they_can_cross_the_gutter(): void
    {
        $view = (string) file_get_contents(dirname(__DIR__, 5) . '/app/Modules/Characters/Views/show.php');
        $css = (string) file_get_contents(dirname(__DIR__, 5) . '/assets/css/modules/characters/open-ledger.css');

        self::assertStringContainsString('$nameLength = mb_strlen($name);', $view);
        self::assertStringContainsString('$nameLength > 42', $view);
        self::assertStringContainsString('gmrc-character-name--wrap', $view);
        self::assertStringContainsString('overflow-wrap: anywhere;', $css);
        self::assertStringContainsString('max-width: 22ch;', $css);
    }
}
