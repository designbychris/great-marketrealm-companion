<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Dashboard;

use PHPUnit\Framework\TestCase;

final class GuildHallDashboardContractTest extends TestCase
{
    public function testDashboardWelcomesPlayerBackToGuildHall(): void
    {
        $root = dirname(__DIR__, 4);

        $view = file_get_contents(
            $root
            . '/app/Modules/Dashboard/Views/'
            . 'index.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'Welcome back to the Guild Hall.',
            $view
        );

        self::assertStringContainsString(
            'components.guild-hall.auby-desk',
            $view
        );

        self::assertStringContainsString(
            'Your Companion map',
            $view
        );

        self::assertStringNotContainsString(
            'Project Leather Satchel',
            $view
        );
    }
}
