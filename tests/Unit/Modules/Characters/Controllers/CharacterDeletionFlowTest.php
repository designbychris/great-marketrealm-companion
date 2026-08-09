<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Controllers;

use PHPUnit\Framework\TestCase;

final class CharacterDeletionFlowTest extends TestCase
{
    public function testRoutesIncludeFinalFarewellConfirmation(): void
    {
        $root = dirname(__DIR__, 5);

        $routes = file_get_contents(
            $root
            . '/app/Modules/Characters/Routes.php'
        );

        self::assertIsString($routes);

        self::assertStringContainsString(
            "'/characters/{id}/delete'",
            $routes
        );

        self::assertStringContainsString(
            "'confirmDelete'",
            $routes
        );
    }

    public function testConfirmationViewPostsDeleteWithCharacterNonce(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'delete.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'value="DELETE"',
            $view
        );

        self::assertStringContainsString(
            "'gmrc_delete_character_'",
            $view
        );

        self::assertStringContainsString(
            'Delete Adventurer',
            file_get_contents(
                $root
                . '/app/Modules/Characters/Views/'
                . 'show.php'
            )
        );
    }

    public function testFinalFarewellStylesAreEnqueued(): void
    {
        $root = dirname(__DIR__, 5);

        $provider = file_get_contents(
            $root
            . '/app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        self::assertIsString($provider);

        self::assertStringContainsString(
            'gmrc-final-farewell',
            $provider
        );

        self::assertFileExists(
            $root
            . '/assets/css/modules/characters/'
            . 'final-farewell.css'
        );
    }
}
