<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Portraits;

use PHPUnit\Framework\TestCase;

final class GuildWardrobePersistenceTest extends TestCase
{
    public function testCreateAndEditFormsBridgeAllWardrobeSlots(): void
    {
        $root = dirname(__DIR__, 5);

        $create = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/create.php'
        );

        $edit = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/edit.php'
        );

        self::assertIsString($create);
        self::assertIsString($edit);

        foreach ([
            'data-portrait-field="outfit"',
            'data-portrait-field="equipment"',
            'data-portrait-field="class_accessory"',
            'data-portrait-field="class_effects"',
            'data-portrait-field="guild_ornament"',
        ] as $field) {
            self::assertStringContainsString(
                $field,
                $create
            );

            self::assertStringContainsString(
                $field,
                $edit
            );
        }
    }

    public function testTrustedRecipeRequiresNewWardrobeSlots(): void
    {
        $root = dirname(__DIR__, 5);

        $factory = file_get_contents(
            $root
            . '/app/Modules/Characters/Portraits/Services/'
            . 'SubmittedPortraitRecipeFactory.php'
        );

        self::assertIsString($factory);

        self::assertStringContainsString(
            "'class_effects'",
            $factory
        );

        self::assertStringContainsString(
            "'guild_ornament'",
            $factory
        );
    }
}
