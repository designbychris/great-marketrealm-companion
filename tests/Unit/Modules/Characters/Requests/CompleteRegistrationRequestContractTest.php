<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Requests;

use PHPUnit\Framework\TestCase;

final class CompleteRegistrationRequestContractTest extends TestCase
{
    public function testStoreRequestAddsCompleteRegistrationWithoutReplacingCoreContract(): void
    {
        $root = dirname(__DIR__, 5);

        $request = file_get_contents(
            $root
            . '/app/Modules/Characters/Requests/'
            . 'StoreCharacterRequest.php'
        );

        self::assertIsString($request);
        self::assertStringContainsString(
            'public function registrationData(): array',
            $request
        );
        self::assertStringContainsString(
            'use ResolvesRegistrationInput',
            $request
        );
    }

    public function testRegistrarSubmitsStrictCompletionFlag(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/create.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'name="registration_confirmed"',
            $view
        );
        self::assertStringContainsString(
            'Seal the Guild Record',
            $view
        );
    }
}
