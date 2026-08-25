<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardHeritageMechanicsInheritanceTest extends TestCase
{
    public function testWorkshopStoresMechanicsInsideEachHeritage(): void
    {
        $root = dirname(__DIR__, 4);
        $source = file_get_contents($root . '/app/Modules/Administration/Workshop/FolkWorkshop.php');
        $view = file_get_contents($root . '/app/Modules/Administration/Views/folk-workshop.php');

        self::assertIsString($source);
        self::assertIsString($view);
        self::assertStringContainsString("'mechanics' => \$mechanics", $source);
        self::assertStringContainsString('heritage_mechanics[', $view);
        self::assertStringContainsString('Heritage mechanics &amp; inheritance', $view);
    }

    public function testResolverAddsHeritageMechanicsToParentFolk(): void
    {
        $root = dirname(__DIR__, 4);
        $source = file_get_contents($root . '/app/Modules/Characters/Services/StewardFolkMechanics.php');

        self::assertIsString($source);
        self::assertStringContainsString('public function resolved(string $race, ?string $heritage = null)', $source);
        self::assertStringContainsString('return $this->mergeMechanics($base, $addition);', $source);
        self::assertStringContainsString('array_merge($left, $right)', $source);
        self::assertStringContainsString('+ (int) ($additionAbilities[$ability] ?? 0)', $source);
    }

    public function testSelectedHeritageReachesCharacterFactoryBeforeHpCalculation(): void
    {
        $root = dirname(__DIR__, 4);
        $factory = file_get_contents($root . '/app/Modules/Characters/Services/CharacterFactory.php');
        $controller = file_get_contents($root . '/app/Modules/Characters/Controllers/CharacterController.php');

        self::assertIsString($factory);
        self::assertIsString($controller);
        self::assertStringContainsString('?string $heritage = null', $factory);
        self::assertStringContainsString("heritage: \$catalogueData['heritage']", $controller);
        self::assertLessThan(
            strpos($factory, 'startingHitPoints'),
            strpos($factory, 'applyAbilityModifiers')
        );
    }

    public function testHeritageSkillsJoinTheExistingImmutableFolkSnapshot(): void
    {
        $root = dirname(__DIR__, 4);
        $factory = file_get_contents($root . '/app/Modules/Characters/Services/CharacterFactory.php');
        $repository = file_get_contents($root . '/app/Modules/Characters/Repositories/CharacterRepository.php');

        self::assertIsString($factory);
        self::assertIsString($repository);
        self::assertStringContainsString('$folkMechanics->skills(', $factory);
        self::assertStringContainsString('_gmrc_racial_skills', $repository);
    }
}
