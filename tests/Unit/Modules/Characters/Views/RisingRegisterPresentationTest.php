<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;
use PHPUnit\Framework\TestCase;
final class RisingRegisterPresentationTest extends TestCase
{
    public function testLedgerContainsProgressionTabAndForms(): void
    {
        $root=dirname(__DIR__,5);$view=file_get_contents($root.'/app/Modules/Characters/Views/show.php');self::assertIsString($view);
        self::assertStringContainsString('data-ledger-tab="progression"',$view);self::assertStringContainsString('data-ledger-panel="progression"',$view);self::assertStringContainsString('/progression/experience',$view);self::assertStringNotContainsString('/progression/level-up',$view);
    }
    public function testProgressionStylesAreRegistered(): void
    {
        $root=dirname(__DIR__,5);$provider=file_get_contents($root.'/app/Providers/FrontendServiceProvider.php');self::assertIsString($provider);self::assertStringContainsString('gmrc-rising-register',$provider);self::assertFileExists($root.'/assets/css/modules/characters/rising-register.css');
    }
}
