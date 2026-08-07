<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Views;
use PHPUnit\Framework\TestCase;
final class AubyDeskLivingSceneTest extends TestCase{public function testSixRasterDeskScenesExist():void{$root=dirname(__DIR__,3);foreach(['dawn','morning','afternoon','evening','night','late-night'] as $scene){self::assertFileExists($root.'/assets/images/auby/desk/scenes/auby-desk-'.$scene.'.webp');}}public function testSceneManifestContainsSixDayparts():void{$root=dirname(__DIR__,3);$manifest=json_decode((string)file_get_contents($root.'/assets/images/auby/desk/scenes/manifest.json'),true);self::assertIsArray($manifest);self::assertCount(6,$manifest['scenes']??[]);}public function testDeskUsesBackgroundSceneRatherThanImageElement():void{$root=dirname(__DIR__,3);$view=file_get_contents($root.'/app/Views/components/guild-hall/auby-desk.php');self::assertIsString($view);self::assertStringContainsString('--gmrc-auby-desk-scene',$view);self::assertStringNotContainsString('data-auby-desk-image',$view);}}
