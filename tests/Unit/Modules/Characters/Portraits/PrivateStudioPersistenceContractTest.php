<?php
declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits;
use PHPUnit\Framework\TestCase;

final class PrivateStudioPersistenceContractTest extends TestCase
{
    public function testUpdateRequestAndControllerPersistFallbackRecipe(): void
    {
        $root = dirname(__DIR__, 5);
        $request = file_get_contents($root . '/app/Modules/Characters/Requests/UpdateCharacterRequest.php');
        $controller = file_get_contents($root . '/app/Modules/Characters/Controllers/CharacterController.php');
        self::assertIsString($request);
        self::assertIsString($controller);
        self::assertStringContainsString('public function portraitData(): array', $request);
        self::assertStringContainsString('$request->portraitData()', $controller);
        self::assertStringContainsString('CharacterPortrait::custom(', $controller);
        self::assertStringContainsString('CharacterPortrait::generated(', $controller);
    }

    public function testRendererProvidesGeneratedWorkbenchFallback(): void
    {
        $root = dirname(__DIR__, 5);
        $renderer = file_get_contents($root . '/app/Modules/Characters/Portraits/Services/PortraitRenderer.php');
        self::assertIsString($renderer);
        self::assertStringContainsString('public function forWorkbench(', $renderer);
    }
}
