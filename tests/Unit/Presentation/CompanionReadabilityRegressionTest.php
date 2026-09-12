<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class CompanionReadabilityRegressionTest extends TestCase
{
    public function test_companion_shell_keeps_a_readable_operational_type_floor(): void
    {
        $css = file_get_contents(dirname(__DIR__, 3) . '/assets/css/companion-app.css');
        self::assertIsString($css);
        self::assertStringContainsString('The Registrar Enlarges the Fine Print', $css);
        self::assertStringContainsString('font-size: 16px;', $css);
        self::assertStringContainsString('.gmrc-navigation__item {', $css);
        self::assertStringContainsString('font-size: 15px;', $css);
        self::assertStringContainsString('.gmrc-app-shell :where(input, select, textarea)', $css);
    }
}
