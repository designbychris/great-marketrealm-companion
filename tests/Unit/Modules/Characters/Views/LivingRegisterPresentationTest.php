<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class LivingRegisterPresentationTest extends TestCase
{
    public function testProgressionSpreadContainsLivingRegister(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents($root . '/app/Modules/Characters/Views/show.php');

        self::assertIsString($view);
        self::assertStringContainsString('data-living-register', $view);
        self::assertStringContainsString('The Living Register', $view);
        self::assertStringContainsString('Current certified progression record', $view);
        self::assertStringContainsString("\$livingRegister['path_gift_count']", $view);
        self::assertStringContainsString('Fresh Ink in the Register', $view);
        self::assertStringContainsString("\$livingRegister['fresh_ink']", $view);
        self::assertStringContainsString('Next Guild Certification', $view);
    }

    public function testLivingRegisterStylesAreRegistered(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents($root . '/app/Providers/FrontendServiceProvider.php');

        self::assertIsString($provider);
        self::assertStringContainsString('gmrc-living-register', $provider);
        self::assertFileExists(
            $root . '/assets/css/modules/characters/living-register.css'
        );
    }
}
