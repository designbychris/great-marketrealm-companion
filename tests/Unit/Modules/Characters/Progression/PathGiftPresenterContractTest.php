<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class PathGiftPresenterContractTest extends TestCase
{
    public function testCharacterControllerPassesCertifiedPathGiftsToLedger(): void
    {
        $root = dirname(__DIR__, 5);

        $controller = file_get_contents(
            $root
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($controller);

        self::assertStringContainsString(
            'new PathGiftPresenter()',
            $controller
        );

        self::assertStringContainsString(
            "'pathGifts' => \$pathGifts",
            $controller
        );
    }
}
