<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class CompleteRegistrationPresentationTest extends TestCase
{
    public function testCreateFlowIncludesCompleteRegistrationStages(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/create.php'
        );

        self::assertIsString($view);

        foreach (
            [
                'Choose a background',
                'Assign the Standard Guild Array',
                'Complete your proficiencies',
                'Review the Guild Record',
                'Seal the Guild Record',
            ]
            as $heading
        ) {
            self::assertStringContainsString(
                $heading,
                $view
            );
        }
    }

    public function testEditFlowCanResolveBackgroundChoices(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/edit.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'Complete background choices',
            $view
        );
        self::assertStringContainsString(
            'registration_confirmed',
            $view
        );
    }

    public function testCompleteRegistrationAssetsAreEnqueued(): void
    {
        $root = dirname(__DIR__, 5);

        $provider = file_get_contents(
            $root
            . '/app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString(
            'gmrc-complete-registration',
            $provider
        );
        self::assertFileExists(
            $root
            . '/assets/css/modules/characters/'
            . 'complete-registration.css'
        );
        self::assertFileExists(
            $root
            . '/assets/js/modules/characters/'
            . 'complete-registration.js'
        );
    }
}
