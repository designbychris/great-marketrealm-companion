<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Views;
use PHPUnit\Framework\TestCase;
final class AubyDeskAmbientEffectsTest extends TestCase{public function testDeskIncludesAmbientEffectLayers():void{$root=dirname(__DIR__,3);$view=file_get_contents($root.'/app/Views/components/guild-hall/auby-desk.php');self::assertIsString($view);foreach(['window-glow','lamp-glow','steam','dust','stars','sleep'] as $effect){self::assertStringContainsString('data-auby-ambient="'.$effect.'"',$view);}}public function testAmbientAnimationRespectsReducedMotion():void{$root=dirname(__DIR__,3);$css=file_get_contents($root.'/assets/css/components/guild-hall/auby-desk.css');self::assertIsString($css);self::assertStringContainsString('prefers-reduced-motion:reduce',$css);self::assertStringContainsString('gmrc-auby-steam',$css);self::assertStringContainsString('gmrc-auby-dust',$css);}}
