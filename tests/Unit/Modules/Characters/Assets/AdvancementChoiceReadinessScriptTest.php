<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Assets;

use PHPUnit\Framework\TestCase;

final class AdvancementChoiceReadinessScriptTest extends TestCase
{
    public function testScriptCountsMultiChoicesAndControlsSubmitReadiness(): void
    {
        $root = dirname(__DIR__, 5);

        $script = file_get_contents(
            $root
            . '/assets/js/modules/characters/'
            . 'advancement-choice-readiness.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'input[name="choice[]"]:checked',
            $script
        );

        self::assertStringContainsString(
            'count >= minimum',
            $script
        );

        self::assertStringContainsString(
            'count <= maximum',
            $script
        );

        self::assertStringContainsString(
            'button.disabled = !ready',
            $script
        );

        self::assertStringContainsString(
            'ready to record.',
            $script
        );

        self::assertStringContainsString(
            'choose ',
            $script
        );
    }

    public function testScriptKeepsServerAsValidationBoundary(): void
    {
        $root = dirname(__DIR__, 5);

        $script = file_get_contents(
            $root
            . '/assets/js/modules/characters/'
            . 'advancement-choice-readiness.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'The server still validates every submitted choice.',
            $script
        );
    }
}
