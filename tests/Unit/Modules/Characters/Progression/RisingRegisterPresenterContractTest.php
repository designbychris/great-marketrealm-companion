<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class RisingRegisterPresenterContractTest extends TestCase
{
    public function testPresenterDoesNotRequireManualLevelUpState(): void
    {
        $root = dirname(__DIR__, 5);

        $presenter = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/Services/'
            . 'RisingRegisterPresenter.php'
        );

        self::assertIsString($presenter);

        self::assertStringNotContainsString(
            '->canLevelUp()',
            $presenter
        );

        self::assertStringContainsString(
            "'can_level_up' => false",
            $presenter
        );
    }
}
