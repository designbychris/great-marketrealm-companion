<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits;

use PHPUnit\Framework\TestCase;

final class PortraitPersistenceContextRegressionTest extends TestCase
{
    public function testCreatePortraitIsExplicitlyProvisional(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/create.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            "'portraitPersisted' => false",
            $view
        );
    }

    public function testPersistedCharacterViewsDeclarePersistedPortraits(): void
    {
        $root = dirname(__DIR__, 5);

        foreach (
            [
                '/app/Modules/Characters/Views/edit.php',
                '/app/Modules/Characters/Views/show.php',
                '/app/Modules/Characters/Views/delete.php',
            ]
            as $relative
        ) {
            $view = file_get_contents(
                $root . $relative
            );

            self::assertIsString($view);

            self::assertStringContainsString(
                "'portraitPersisted' => true",
                $view
            );
        }
    }

    public function testPortraitComponentDoesNotInferPersistenceFromViewModel(): void
    {
        $root = dirname(__DIR__, 5);

        $component = file_get_contents(
            $root
            . '/app/Views/components/media/'
            . 'illuminated-portrait.php'
        );

        self::assertIsString($component);

        self::assertStringContainsString(
            '$portraitPersisted = isset($portraitPersisted)',
            $component
        );

        self::assertStringNotContainsString(
            'data-portrait-persisted="<?php echo $portraitModel',
            $component
        );
    }
}
