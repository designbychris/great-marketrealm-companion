<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketTrainingRegressionTest extends TestCase
{
    public function testCanonicalCharacterStatisticsAreUsed(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketApi.php');
        self::assertIsString($source);
        self::assertStringContainsString('$character->savingThrows()', $source);
        self::assertStringContainsString('$character->skills()->all()', $source);
        self::assertStringContainsString("'saving_throws' => \$savingThrowData", $source);
        self::assertStringContainsString("'skills' => \$skillData", $source);
        self::assertStringContainsString("'expertise' => \$skill->hasExpertise()", $source);
    }

    public function testQuickChecksUseSharedDiceworks(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertIsString($source);
        self::assertStringContainsString('tray.rollCheck=(label,mod)=>{tray.open=true;performRoll(20,1,mod', $source);
        self::assertStringContainsString("diceTray.rollCheck(name+(isSkill?' skill check':' saving throw'),data.modifier)", $source);
        self::assertStringContainsString("pocketTraining('Saving throws',character.saving_throws,diceTray,false)", $source);
        self::assertStringContainsString("pocketTraining('Skills',character.skills,diceTray,true)", $source);
    }
}
