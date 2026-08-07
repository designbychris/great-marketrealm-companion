<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubySealApprovalBaselineTest extends TestCase
{
    public function testApprovalArcIsLowerThanPreviousBaseline(): void
    {
        $root = dirname(__DIR__, 3);

        $seal = file_get_contents(
            $root
            . '/assets/images/auby/seals/'
            . 'seal-of-approval.svg'
        );

        self::assertIsString($seal);

        self::assertStringContainsString(
            'd="M59 200',
            $seal
        );

        self::assertStringContainsString(
            'A106 106 0 0 0 268 200',
            $seal
        );

        self::assertStringContainsString(
            '>APPROVAL</textPath>',
            $seal
        );
    }
}
