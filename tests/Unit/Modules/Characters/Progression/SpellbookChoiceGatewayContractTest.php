<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class SpellbookChoiceGatewayContractTest extends TestCase
{
    public function testChoiceControllerUsesRequirementResolverInsteadOfVitalityOnlyGuard(): void
    {
        $root = dirname(__DIR__, 5);
        $controller = file_get_contents(
            $root . '/app/Modules/Characters/Controllers/CharacterController.php'
        );
        self::assertIsString($controller);
        self::assertStringContainsString('AdvancementChoiceRequirementResolver', $controller);
        self::assertStringNotContainsString("$choiceKey !== 'vitality-hit-points'", $controller);
    }
}
