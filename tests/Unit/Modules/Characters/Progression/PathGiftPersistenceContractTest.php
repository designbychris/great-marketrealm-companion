<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class PathGiftPersistenceContractTest extends TestCase
{
    public function testRepositoryPersistsAndHydratesPathGifts(): void
    {
        $root = dirname(__DIR__, 5);

        $repository = file_get_contents(
            $root
            . '/app/Modules/Characters/Repositories/'
            . 'CharacterRepository.php'
        );

        self::assertIsString($repository);

        self::assertStringContainsString(
            "META_PATH_GIFTS = '_gmrc_path_gifts'",
            $repository
        );

        self::assertStringContainsString(
            '$character->pathGifts()->values()',
            $repository
        );

        self::assertStringContainsString(
            'mapPathGifts(',
            $repository
        );

        self::assertStringContainsString(
            '$persistedPathGifts->values()',
            $repository
        );
    }

    public function testCertificationGrantsAndArchivesPathGiftsBeforeSave(): void
    {
        $root = dirname(__DIR__, 5);

        $service = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Services/GuildCertificationService.php'
        );

        self::assertIsString($service);

        self::assertStringContainsString(
            'grantPathGifts(',
            $service
        );

        self::assertStringContainsString(
            "'path_gifts_granted' =>",
            $service
        );

        $grantPosition = strpos(
            $service,
            'grantPathGifts('
        );

        $savePosition = strpos(
            $service,
            '$this->characters->save('
        );

        self::assertIsInt($grantPosition);
        self::assertIsInt($savePosition);

        self::assertLessThan(
            $savePosition,
            $grantPosition
        );
    }
}
