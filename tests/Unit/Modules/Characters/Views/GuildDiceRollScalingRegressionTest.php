<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceRollScalingRegressionTest extends TestCase
{
    public function testArcaneRollTriggerReceivesPhpResolvedScalingMetadata(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'class="gmrc-arcane-scaling"',
            $view
        );
        self::assertStringContainsString(
            'data-roll-base-formula=',
            $view
        );
        self::assertStringContainsString(
            'data-roll-scaling-source=',
            $view
        );
        self::assertStringContainsString(
            'data-roll-scaling-at=',
            $view
        );
        self::assertStringContainsString(
            'Scaled by adventurer level',
            $view
        );
        self::assertStringContainsString(
            'Prepared for higher-slot scaling',
            $view
        );
    }

    public function testDiceworksConsumesResolvedFormulaWithoutScalingRules(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'formula: activeTrigger.dataset.rollFormula',
            $script
        );
        self::assertStringContainsString(
            'baseFormula: activeTrigger.dataset.rollBaseFormula',
            $script
        );
        self::assertStringContainsString(
            'scalingSource:',
            $script
        );
        self::assertStringNotContainsString(
            'characterLevelScaling',
            $script
        );
        self::assertStringNotContainsString(
            'slotLevelScaling',
            $script
        );
        self::assertStringNotContainsString(
            'featureRankScaling',
            $script
        );
    }

    public function testCharacterQuickRollReferenceDoesNotFreezeScaledFormula(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const triggerReference = function (trigger)',
            $script
        );
        self::assertStringContainsString(
            'const findTriggerForFavourite = function (entry)',
            $script
        );
        self::assertStringContainsString(
            'trigger.dataset.rollLabel === entry.label',
            $script
        );

        $referenceStart = strpos(
            $script,
            'const triggerReference = function (trigger)'
        );
        $referenceEnd = strpos(
            $script,
            'const findTriggerByReference',
            $referenceStart
        );
        $referenceBlock = substr(
            $script,
            $referenceStart,
            $referenceEnd - $referenceStart
        );

        self::assertStringNotContainsString(
            'rollFormula',
            $referenceBlock
        );
    }
}
