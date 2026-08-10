<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;
use PHPUnit\Framework\TestCase;
final class GrandCataloguePresentationTest extends TestCase
{
    public function testCreateViewHasDependentCatalogueSelectors(): void
    {
        $root=dirname(__DIR__,5);$view=file_get_contents($root.'/app/Modules/Characters/Views/create.php');self::assertIsString($view);
        self::assertStringContainsString('name="heritage"',$view);
        self::assertStringContainsString('name="subclass"',$view);
        self::assertStringContainsString('data-catalogue-child="heritage"',$view);
        self::assertStringContainsString('data-catalogue-child="subclass"',$view);
    }
    public function testGrandCatalogueAssetsAreRegistered(): void
    {
        $root=dirname(__DIR__,5);$provider=file_get_contents($root.'/app/Providers/FrontendServiceProvider.php');self::assertIsString($provider);
        self::assertStringContainsString('gmrc-grand-catalogue',$provider);
        self::assertFileExists($root.'/assets/css/modules/characters/grand-catalogue.css');
        self::assertFileExists($root.'/assets/js/modules/characters/grand-catalogue.js');
    }
}
