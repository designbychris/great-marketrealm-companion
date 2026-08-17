<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Vitals;

use PHPUnit\Framework\TestCase;

final class TargetAwareVitalApplicationRegressionTest extends TestCase
{
    public function testDiceworksExposesTargetAwareVitalApplicationUi(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'data-guild-vital-application',
            $view
        );
        self::assertStringContainsString(
            'data-guild-vital-application-note',
            $view
        );
        self::assertStringContainsString(
            'data-guild-vital-apply',
            $view
        );
        self::assertStringContainsString(
            'Resolved targets may support Vital Application',
            $view
        );
    }

    public function testOnlyResolvedSelfCanMutateCurrentCharacterVitals(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'const selfTargetCanMutate = function (target)',
            $script
        );
        self::assertStringContainsString(
            'target.resolved === true',
            $script
        );
        self::assertStringContainsString(
            "target.kind === 'self'",
            $script
        );
        self::assertStringContainsString(
            'target.id === characterId',
            $script
        );
        self::assertStringContainsString(
            'vitalForm instanceof HTMLFormElement',
            $script
        );
    }

    public function testReferenceTargetsRemainNonMutatingUntilRegistryResolution(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'This target is reference-only until ',
            $script
        );
        self::assertStringContainsString(
            'the party or encounter registry can resolve it.',
            $script
        );
        self::assertStringContainsString(
            'clearVitalApplication();',
            $script
        );
    }

    public function testApplicationReusesExistingVitalMeasuresFormAndRules(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );
        $ledger = file_get_contents(
            $root . '/assets/js/modules/characters/living-ledger.js'
        );

        self::assertIsString($script);
        self::assertIsString($ledger);
        self::assertStringContainsString(
            "vitalMeasures.querySelector(",
            $script
        );
        self::assertStringContainsString(
            "'[data-vital-amount]'",
            $script
        );
        self::assertStringContainsString(
            "'[data-vital-action=\"'",
            $script
        );
        self::assertStringContainsString(
            'actionButton.click();',
            $script
        );
        self::assertStringContainsString(
            'vitalForm.requestSubmit();',
            $script
        );
        self::assertStringContainsString(
            'temporary.value = String(tempValue - absorbed);',
            $ledger
        );
        self::assertStringContainsString(
            "action === 'heal'",
            $ledger
        );
    }

    public function testDamageHealingAndCriticalDamagePrepareApplicationsButD20DoesNot(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString(
            'prepareVitalApplication(',
            $script
        );
        self::assertStringContainsString(
            "selection.kind,",
            $script
        );
        self::assertStringContainsString(
            "'damage',",
            $script
        );
        self::assertStringContainsString(
            'const performD20 = function (selection, mode)',
            $script
        );
        self::assertStringContainsString(
            'clearVitalApplication();',
            $script
        );
        self::assertStringContainsString(
            'pendingVitalApplication.amount',
            $script
        );
    }
}
