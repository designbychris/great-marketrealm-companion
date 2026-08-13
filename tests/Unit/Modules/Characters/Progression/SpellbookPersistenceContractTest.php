<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class SpellbookPersistenceContractTest extends TestCase
{
    public function testCharacterRepositoryPersistsSpellbookInsideCharacterSave(): void
    {
        $root = dirname(__DIR__, 5);
        $repository = file_get_contents(
            $root . '/app/Modules/Characters/Repositories/CharacterRepository.php'
        );
        self::assertIsString($repository);
        self::assertStringContainsString('_gmrc_spellbook', $repository);
        self::assertStringContainsString('$character->spellbook()->toArray()', $repository);
        self::assertStringContainsString('Spellbook::fromArray', $repository);
    }

    public function testCertificationLearnsArcanaBeforeSingleCharacterSave(): void
    {
        $root = dirname(__DIR__, 5);
        $service = file_get_contents(
            $root . '/app/Modules/Characters/Progression/Services/GuildCertificationService.php'
        );
        self::assertIsString($service);
        $learn = strpos($service, '$character->learnArcana(');
        $save = strpos($service, '$this->characters->save(');
        self::assertIsInt($learn);
        self::assertIsInt($save);
        self::assertLessThan($save, $learn);
    }
}
