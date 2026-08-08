<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Dashboard;

use PHPUnit\Framework\TestCase;

final class GuildWelcomePresentationTest extends TestCase
{
    public function testDashboardUsesCenteredGuildWelcomeParchment(): void
    {
        $root = dirname(__DIR__, 4);

        $view = file_get_contents(
            $root
            . '/app/Modules/Dashboard/Views/index.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'gmrc-guild-welcome__paper',
            $view
        );

        self::assertStringContainsString(
            'gmrc-guild-welcome__tape--left',
            $view
        );

        self::assertStringContainsString(
            'gmrc-guild-welcome__tape--right',
            $view
        );

        self::assertStringContainsString(
            'gmrc-guild-welcome__divider',
            $view
        );

        self::assertStringContainsString(
            'Welcome back to the Guild Hall.',
            $view
        );
    }

    public function testGuildWelcomeUsesHandwrittenTypographyAndPurpleTape(): void
    {
        $root = dirname(__DIR__, 4);

        $css = file_get_contents(
            $root
            . '/assets/css/modules/dashboard/'
            . 'guild-hall-dashboard.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '--gmrc-font-handwritten',
            $css
        );

        self::assertStringContainsString(
            '#653d8f',
            $css
        );

        self::assertStringContainsString(
            'text-align: center',
            $css
        );
    }
}
