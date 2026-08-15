<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class AdvancementHistoryPresentationTest extends TestCase
{
    public function testCompletedCertificationsMoveIntoSealedChronicle(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        self::assertStringNotContainsString(
            'gmrc-rise-certification-history',
            $view
        );

        self::assertStringNotContainsString(
            'Certified Advancements',
            $view
        );

        self::assertStringContainsString(
            'data-sealed-chronicle',
            $view
        );

        self::assertStringContainsString(
            'The Sealed Chronicle',
            $view
        );

        self::assertStringContainsString(
            "\$livingRegister['chronicle']",
            $view
        );
    }
}
