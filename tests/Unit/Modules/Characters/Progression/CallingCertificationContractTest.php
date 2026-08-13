<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class CallingCertificationContractTest extends TestCase
{
    public function testCertificationArchivesResolvedCallingProgression(): void
    {
        $root = dirname(__DIR__, 5);

        $service = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Services/GuildCertificationService.php'
        );

        self::assertIsString($service);

        self::assertStringContainsString(
            "'calling' => is_array(",
            $service
        );

        self::assertStringContainsString(
            "'class_progression'",
            $service
        );
    }

    public function testCallingDoesNotOwnFutureSpecialistChoices(): void
    {
        $root = dirname(__DIR__, 5);

        $wizard = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Definitions/Classes/WizardProgression.php'
        );

        self::assertIsString($wizard);

        self::assertStringContainsString(
            "'phase' => 'III.8.7'",
            $wizard
        );

        self::assertStringContainsString(
            "'phase' => 'III.8.8'",
            $wizard
        );

        self::assertStringContainsString(
            "'phase' => 'III.8.9'",
            $wizard
        );
    }
}
