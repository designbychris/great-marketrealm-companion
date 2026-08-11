<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Scripts;

use PHPUnit\Framework\TestCase;

final class GuildWardrobeWorkbenchTest extends TestCase
{
    public function testWorkbenchDiscoversEveryWardrobeSlot(): void
    {
        $root = dirname(__DIR__, 5);

        $variants = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/variants.js'
        );

        $controls = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/controls.js'
        );

        $updater = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/layer-updater.js'
        );

        self::assertIsString($variants);
        self::assertIsString($controls);
        self::assertIsString($updater);

        foreach ([
            'class_accessory',
            'class_effects',
            'guild_ornament',
        ] as $slot) {
            self::assertStringContainsString(
                $slot,
                $variants
            );

            self::assertStringContainsString(
                $slot,
                $controls
            );

            self::assertStringContainsString(
                $slot,
                $updater
            );
        }
    }

    public function testOptionalWardrobeLayersExposeNoneVariant(): void
    {
        $root = dirname(__DIR__, 5);

        $variants = file_get_contents(
            $root
            . '/assets/js/components/media/'
            . 'portrait-studio/variants.js'
        );

        self::assertIsString($variants);

        self::assertStringContainsString(
            "'-accessory-none'",
            $variants
        );

        self::assertStringContainsString(
            "'-effects-none'",
            $variants
        );

        self::assertStringContainsString(
            "'-ornament-none'",
            $variants
        );
    }
}
