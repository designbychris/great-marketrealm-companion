<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Vitals;

use PHPUnit\Framework\TestCase;

final class VitalMeasuresContractTest extends TestCase
{
    public function testCharacterRoutesExposeMutableVitalMeasures(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = file_get_contents($root . '/app/Modules/Characters/Routes.php');
        $controller = file_get_contents(
            $root . '/app/Modules/Characters/Controllers/CharacterController.php'
        );

        self::assertIsString($routes);
        self::assertIsString($controller);
        self::assertStringContainsString(
            "'/characters/{id}/vital-measures'",
            $routes
        );
        self::assertStringContainsString(
            "[CharacterController::class, 'updateVitalMeasures']",
            $routes
        );
        self::assertStringContainsString(
            'public function updateVitalMeasures',
            $controller
        );
        self::assertStringContainsString(
            '$character->updateVitalMeasures(',
            $controller
        );
        self::assertStringContainsString(
            '$this->characters->save($character);',
            $controller
        );

        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString(
            '#^characters/([^/]+)/vital-measures$#',
            $provider
        );
        self::assertStringContainsString(
            "return 'gmrc_character_vitals_'",
            $provider
        );
    }

    public function testLedgerAllowsCurrentAndTemporaryHpButKeepsMaximumReadOnly(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/living-ledger.js'
        );

        self::assertIsString($view);
        self::assertIsString($script);
        self::assertStringContainsString('data-vital-measures', $view);
        self::assertStringContainsString('name="current_hp"', $view);
        self::assertStringContainsString('name="temporary_hp"', $view);
        self::assertStringNotContainsString('name="maximum_hp"', $view);
        self::assertStringContainsString('Guild certified', $view);
        self::assertStringContainsString('data-vital-action="damage"', $view);
        self::assertStringContainsString('data-vital-action="heal"', $view);
        self::assertStringContainsString('[data-vital-adjust]', $script);
        self::assertStringContainsString("action === 'damage'", $script);
        self::assertStringContainsString("action === 'heal'", $script);
        self::assertStringContainsString('temporary.value = String(tempValue - absorbed);', $script);
        self::assertStringContainsString('Math.min(', $script);
    }
}
