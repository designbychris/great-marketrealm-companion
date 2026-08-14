<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class AdvancementChoiceReadinessPresentationTest extends TestCase
{
    public function testChooseNFoliosExposeReadinessMetadataAndGuidance(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'data-advancement-choice',
            $view
        );

        self::assertStringContainsString(
            'data-choice-minimum=',
            $view
        );

        self::assertStringContainsString(
            'data-choice-maximum=',
            $view
        );

        self::assertStringContainsString(
            'data-choice-readiness-status',
            $view
        );

        self::assertStringContainsString(
            'Looks like you can learn some',
            $view
        );

        self::assertStringContainsString(
            'to add to your spellbook.',
            $view
        );

        self::assertStringContainsString(
            'data-choice-submit',
            $view
        );

        self::assertStringContainsString(
            'aria-live="polite"',
            $view
        );
    }

    public function testChooseNButtonStartsDisabledUntilSelectionIsValid(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            '! $choiceReady',
            $view
        );

        self::assertStringContainsString(
            'disabled',
            $view
        );

        self::assertStringContainsString(
            'aria-disabled="true"',
            $view
        );
    }
}
