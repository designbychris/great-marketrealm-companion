<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models;

use PHPUnit\Framework\TestCase;

final class CompleteRegistrationCharacterContractTest extends TestCase
{
    public function testCharacterStoresExplicitRegistrationChoices(): void
    {
        $root = dirname(__DIR__, 5);

        $character = file_get_contents(
            $root
            . '/app/Modules/Characters/Models/'
            . 'Character.php'
        );

        self::assertIsString($character);
        self::assertStringContainsString(
            'selectedLanguages',
            $character
        );
        self::assertStringContainsString(
            'selectedToolProficiencies',
            $character
        );
        self::assertStringContainsString(
            'completeRegistration',
            $character
        );
    }

    public function testRepositoryPersistsRegistrationChoices(): void
    {
        $root = dirname(__DIR__, 5);

        $repository = file_get_contents(
            $root
            . '/app/Modules/Characters/Repositories/'
            . 'CharacterRepository.php'
        );

        self::assertIsString($repository);
        self::assertStringContainsString(
            '_gmrc_selected_languages',
            $repository
        );
        self::assertStringContainsString(
            '_gmrc_selected_tools',
            $repository
        );
    }
}
