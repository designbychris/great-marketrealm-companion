<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class CharacterAdvancementPersistenceContractTest extends TestCase
{
    public function testRepositoryVerifiesLevelAfterSavingCharacter(): void
    {
        $root = dirname(__DIR__, 5);

        $repository = file_get_contents(
            $root
            . '/app/Modules/Characters/Repositories/'
            . 'CharacterRepository.php'
        );

        self::assertIsString($repository);

        self::assertStringContainsString(
            'assertPersistedState(',
            $repository
        );

        self::assertStringContainsString(
            'clearPersistenceCache(',
            $repository
        );

        self::assertStringContainsString(
            "'post_meta'",
            $repository
        );

        self::assertStringContainsString(
            '$storedLevel !== $character->level()->value()',
            $repository
        );
    }

    public function testRepositoryRefusesDuplicateDomainCharacterIds(): void
    {
        $root = dirname(__DIR__, 5);

        $repository = file_get_contents(
            $root
            . '/app/Modules/Characters/Repositories/'
            . 'CharacterRepository.php'
        );

        self::assertIsString($repository);

        self::assertStringContainsString(
            "'posts_per_page' => 2",
            $repository
        );

        self::assertStringContainsString(
            'count($posts) > 1',
            $repository
        );

        self::assertStringContainsString(
            'duplicate records',
            $repository
        );
    }
}
