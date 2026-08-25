<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardFolkMechanicsIntegrationTest extends TestCase
{
    public function testWorkshopStoresStructuredBaseFolkMechanics(): void
    {
        $root = dirname(__DIR__, 4);
        $source = file_get_contents($root . '/app/Modules/Administration/Workshop/FolkWorkshop.php');

        self::assertIsString($source);
        self::assertStringContainsString("'ability_modifiers' => \$abilityModifiers", $source);
        self::assertStringContainsString("'skill_proficiencies' => \$skillProficiencies", $source);
        self::assertStringContainsString("'automatic_languages' => \$automaticLanguages", $source);
        self::assertStringContainsString("'tool_proficiencies' => \$toolProficiencies", $source);
    }

    public function testCharacterFactoryAppliesFolkMechanicsBeforeHitPointsAreCalculated(): void
    {
        $root = dirname(__DIR__, 4);
        $source = file_get_contents($root . '/app/Modules/Characters/Services/CharacterFactory.php');

        self::assertIsString($source);
        self::assertStringContainsString('applyAbilityModifiers', $source);
        self::assertStringContainsString('->merge($folkMechanics->languages', $source);
        self::assertStringContainsString('->merge($folkMechanics->tools', $source);
        self::assertLessThan(
            strpos($source, 'startingHitPoints'),
            strpos($source, 'applyAbilityModifiers')
        );
    }

    public function testRacialSkillsAreSnapshottedWithCharacter(): void
    {
        $root = dirname(__DIR__, 4);
        $character = file_get_contents($root . '/app/Modules/Characters/Models/Character.php');
        $repository = file_get_contents($root . '/app/Modules/Characters/Repositories/CharacterRepository.php');

        self::assertIsString($character);
        self::assertIsString($repository);
        self::assertStringContainsString('racialSkillProficiencies', $character);
        self::assertStringContainsString('_gmrc_racial_skills', $repository);
        self::assertStringContainsString('->merge($this->racialSkillProficiencies)', $character);
    }
}
