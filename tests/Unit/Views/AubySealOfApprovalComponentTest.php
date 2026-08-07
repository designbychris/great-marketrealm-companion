<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubySealOfApprovalComponentTest extends TestCase
{
    public function testSealAssetsExist(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (
            [
                'seal-of-approval.svg',
                'seal-ink-splatter.svg',
                'seal-of-approval-one-colour.svg',
                'seal-of-approval-embossed.svg',
                'seal-of-approval-gold.svg',
            ] as $asset
        ) {
            self::assertFileExists(
                $root
                . '/assets/images/auby/seals/'
                . $asset
            );
        }
    }

    public function testReusableSealComponentExists(): void
    {
        $root = dirname(__DIR__, 3);

        self::assertFileExists(
            $root
            . '/app/Views/components/auby/'
            . 'seal-of-approval.php'
        );

        self::assertFileExists(
            $root
            . '/assets/css/components/auby/'
            . 'seal-of-approval.css'
        );

        self::assertFileExists(
            $root
            . '/assets/js/components/auby/'
            . 'seal-of-approval.js'
        );
    }
}
