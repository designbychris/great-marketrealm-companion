<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubySealTypographyRegressionTest extends TestCase
{
    public function testSealKeepsCurvedTextAndOpenInnerRing(): void
    {
        $root = dirname(__DIR__, 3);

        $seal = file_get_contents(
            $root
            . '/assets/images/auby/seals/'
            . 'seal-of-approval.svg'
        );

        self::assertIsString($seal);

        self::assertStringContainsString(
            '<textPath',
            $seal
        );

        self::assertStringContainsString(
            '>SEAL OF</textPath>',
            $seal
        );

        self::assertStringContainsString(
            '>APPROVAL</textPath>',
            $seal
        );

        self::assertStringNotContainsString(
            'r="116"',
            $seal
        );
    }
}
