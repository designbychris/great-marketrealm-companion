<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class Generation2AvailabilityRegressionTest extends TestCase
{
    public function testLiveGenerationTwoMapDoesNotRequireRemovedGuildMark(): void
    {
        $root = dirname(__DIR__, 6);

        $script = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/generation2.js'
        );

        self::assertIsString($script);

        self::assertStringNotContainsString(
            'g2-auby-illuminator-mark-01',
            $script
        );

        self::assertStringContainsString(
            'g2-auby-finishing-touch-01',
            $script
        );
    }

    public function testBenchmarkManifestStillContainsGenerationTwoCollection(): void
    {
        $root = dirname(__DIR__, 6);

        $manifest = json_decode(
            (string) file_get_contents(
                $root
                . '/app/Modules/Characters/Portraits/'
                . 'Library/Generation2/Collections/'
                . 'FructanGrocer/manifest.json'
            ),
            true
        );

        self::assertIsArray($manifest);

        self::assertSame(
            'collection-fructan-grocer',
            $manifest['id'] ?? null
        );

        self::assertSame(
            [
                'fructan',
                'grocer',
            ],
            $manifest['compatible_with'] ?? null
        );
    }

    public function testApprovedAppleBodyRemainsRoundStorybookAsset(): void
    {
        $root = dirname(__DIR__, 6);

        $body = file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/'
            . 'Library/Generation2/Races/Fructan/'
            . 'Assets/apple/body-base.svg'
        );

        self::assertIsString($body);

        self::assertStringContainsString(
            'Approved clean Apple Fructan body',
            $body
        );

        self::assertStringContainsString(
            'radialGradient',
            $body
        );
    }
}
