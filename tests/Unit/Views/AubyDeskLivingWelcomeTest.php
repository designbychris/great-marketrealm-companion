<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class AubyDeskLivingWelcomeTest extends TestCase
{
    public function testEverySceneContainsLivingWelcomeCopy(): void
    {
        $root = dirname(__DIR__, 3);

        $manifest = json_decode(
            (string) file_get_contents(
                $root
                . '/assets/images/auby/desk/scenes/'
                . 'manifest.json'
            ),
            true
        );

        self::assertIsArray($manifest);
        self::assertSame(
            '3.0',
            $manifest['welcome_copy_version'] ?? null
        );

        foreach ($manifest['scenes'] ?? [] as $scene) {
            foreach (
                [
                    'heading',
                    'status',
                    'note_title',
                    'note_message',
                    'tea_message',
                ] as $field
            ) {
                self::assertNotSame(
                    '',
                    trim((string) ($scene[$field] ?? ''))
                );
            }
        }
    }

    public function testDeskMarkupExposesDynamicWelcomeTargets(): void
    {
        $root = dirname(__DIR__, 3);

        $desk = file_get_contents(
            $root
                . '/app/Views/components/'
                . 'guild-hall/auby-desk.php'
        );

        $note = file_get_contents(
            $root
                . '/app/Views/components/auby/'
                . 'sticky-note.php'
        );

        self::assertIsString($desk);
        self::assertIsString($note);

        self::assertStringContainsString(
            'data-auby-desk-title',
            $desk
        );

        self::assertStringContainsString(
            'data-auby-tea-message',
            $desk
        );

        self::assertStringContainsString(
            'data-auby-note-title',
            $note
        );

        self::assertStringContainsString(
            'data-auby-note-message',
            $note
        );
    }

    public function testControllerUpdatesWholeLivingWelcome(): void
    {
        $root = dirname(__DIR__, 3);

        $script = file_get_contents(
            $root
                . '/assets/js/components/'
                . 'guild-hall/auby-desk.js'
        );

        self::assertIsString($script);

        foreach (
            [
                '[data-auby-desk-title]',
                '[data-auby-desk-status]',
                '[data-auby-note-title]',
                '[data-auby-note-message]',
                '[data-auby-tea-message]',
            ] as $selector
        ) {
            self::assertStringContainsString(
                $selector,
                $script
            );
        }

        self::assertStringContainsString(
            'gmrc:guild-hall:daypart-changed',
            $script
        );
    }
}
