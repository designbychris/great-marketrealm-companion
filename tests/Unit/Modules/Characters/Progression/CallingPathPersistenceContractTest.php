<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class CallingPathPersistenceContractTest extends TestCase
{
    public function testCharacterRepositoryUsesExistingSubclassMetadata(): void
    {
        $root = dirname(__DIR__, 5);

        $repository = file_get_contents(
            $root
            . '/app/Modules/Characters/Repositories/'
            . 'CharacterRepository.php'
        );

        self::assertIsString($repository);

        self::assertStringContainsString(
            "META_CALLING_PATH = '_gmrc_subclass'",
            $repository
        );

        self::assertStringContainsString(
            '$character->callingPath()->value()',
            $repository
        );

        self::assertStringContainsString(
            'mapCallingPath(',
            $repository
        );
    }

    public function testGuildCertificationAppliesPathBeforeSavingCharacter(): void
    {
        $root = dirname(__DIR__, 5);

        $service = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Services/GuildCertificationService.php'
        );

        self::assertIsString($service);

        self::assertStringContainsString(
            'chooseCallingPath(',
            $service
        );

        self::assertStringContainsString(
            "'calling_path' =>",
            $service
        );

        $pathPosition = strpos(
            $service,
            'chooseCallingPath('
        );

        $savePosition = strpos(
            $service,
            '$this->characters->save('
        );

        self::assertIsInt($pathPosition);
        self::assertIsInt($savePosition);

        self::assertLessThan(
            $savePosition,
            $pathPosition
        );
    }
}
