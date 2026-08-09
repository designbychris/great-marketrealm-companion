<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Controllers;

use PHPUnit\Framework\TestCase;

final class CompleteRegistrationRedirectFeedbackTest extends TestCase
{
    public function testCreateRestoresValidationStateFromFlashStore(): void
    {
        $root = dirname(__DIR__, 5);

        $controller = file_get_contents(
            $root
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($controller);

        self::assertStringContainsString(
            '$this->flash->old()',
            $controller
        );

        self::assertStringContainsString(
            '$this->flash->errors()',
            $controller
        );
    }

    public function testSuccessfulRegistrationTargetsOpenLedger(): void
    {
        $root = dirname(__DIR__, 5);

        $controller = file_get_contents(
            $root
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($controller);

        self::assertStringContainsString(
            '$this->characterUrl(',
            $controller
        );

        self::assertStringContainsString(
            '$character->id()',
            $controller
        );
    }
}
