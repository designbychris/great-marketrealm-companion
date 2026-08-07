<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits\Generation2;

use PHPUnit\Framework\TestCase;

final class AppleFructanCleanSilhouetteTest extends TestCase
{
    public function testAppleBodyUsesSingleCleanFruitSilhouette(): void
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

        /*
         * The crown coordinate occurs only once in the fruit path.
         * Repeating it previously introduced a second contour at the chin.
         */
        self::assertSame(
            1,
            substr_count(
                $body,
                'M240 126'
            )
        );
    }

    public function testAppleShadowDoesNotAddLowerChinEllipse(): void
    {
        $root = dirname(__DIR__, 6);

        $shadow = file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/'
            . 'Library/Generation2/Races/Fructan/'
            . 'Assets/apple/body-shadow.svg'
        );

        self::assertIsString($shadow);

        self::assertStringNotContainsString(
            '<ellipse',
            $shadow
        );
    }
}
