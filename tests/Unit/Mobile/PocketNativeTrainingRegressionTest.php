<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeTrainingRegressionTest extends TestCase
{
    public function testNativeCharacterTabPresentsAuthoritativeSavingThrowsAndSkills(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString('function trainingPanel(character)', $client);
        self::assertStringContainsString('character.saving_throws || {}', $client);
        self::assertStringContainsString('character.skills || {}', $client);
        self::assertStringContainsString('Skills &amp; Saving Throws', $client);
        self::assertStringContainsString('characterPanel.append(trainingPanel(character));', $client);
    }

    public function testTrainingPresentationDistinguishesProficiencyAndExpertiseWithoutRolling(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');
        $css = file_get_contents($root . '/native/pocket-companion/src/native.css');

        self::assertStringContainsString("skill?.expertise ? 'Expertise'", $client);
        self::assertStringContainsString("skill?.proficient ? 'Proficient' : 'Untrained'", $client);
        self::assertStringContainsString('training-badge--expertise', $client);
        self::assertStringContainsString('.training-row.is-proficient', $css);
        self::assertStringNotContainsString('data-training-roll', $client);
    }

    public function testPocketApiSuppliesSkillLabelsAbilitiesAndResolvedModifiers(): void
    {
        $root = dirname(__DIR__, 3);
        $api = file_get_contents($root . '/app/Mobile/PocketApi.php');

        self::assertIsString($api);
        self::assertStringContainsString("'acrobatics' => ['Acrobatics', 'DEX']", $api);
        self::assertStringContainsString("'animal-handling' => ['Animal Handling', 'WIS']", $api);
        self::assertStringContainsString("'sleight-of-hand' => ['Sleight of Hand', 'DEX']", $api);
        self::assertStringContainsString("'modifier' => \$skill->modifier()", $api);
        self::assertStringContainsString("'proficient' => \$skill->isProficient()", $api);
        self::assertStringContainsString("'expertise' => \$skill->hasExpertise()", $api);
    }
}
