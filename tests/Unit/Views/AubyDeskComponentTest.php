<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Views;
use PHPUnit\Framework\TestCase;
final class AubyDeskComponentTest extends TestCase{public function testDeskComponentAndSceneManifestExist():void{$root=dirname(__DIR__,3);self::assertFileExists($root.'/app/Views/components/guild-hall/auby-desk.php');self::assertFileExists($root.'/assets/images/auby/desk/scenes/manifest.json');}public function testDeskScriptUsesVisitorsLocalHour():void{$root=dirname(__DIR__,3);$script=file_get_contents($root.'/assets/js/components/guild-hall/auby-desk.js');self::assertIsString($script);self::assertStringContainsString('new Date().getHours()',$script);self::assertStringContainsString('sceneForHour',$script);self::assertStringContainsString('manifest.json',$script);}}
