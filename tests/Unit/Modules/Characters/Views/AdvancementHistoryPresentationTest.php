<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class AdvancementHistoryPresentationTest extends TestCase
{
    public function testRisingRegisterShowsCompletedCertifications(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'gmrc-rise-certification-history',
            $view
        );

        self::assertStringContainsString(
            'Certified Advancements',
            $view
        );

        self::assertStringContainsString(
            'Guild Certified — Level',
            $view
        );
    }
}
