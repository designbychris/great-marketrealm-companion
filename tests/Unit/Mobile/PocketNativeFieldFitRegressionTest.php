<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
final class PocketNativeFieldFitRegressionTest extends TestCase
{
 public function testNativeFullScreenViewsOwnTheViewportInsteadOfDoublePadding(): void
 {
  $root=dirname(__DIR__,3);
  $client=file_get_contents($root.'/native/pocket-companion/src/main.js');
  $css=file_get_contents($root.'/native/pocket-companion/src/native.css');
  self::assertStringContainsString("const nativeShell = document.querySelector('.native-shell');",$client);
  self::assertStringContainsString('nativeShell.dataset.view = view;',$client);
  self::assertStringContainsString('.native-shell[data-view="character"]',$css);
  self::assertStringContainsString('max-width:none;',$css);
 }
 public function testCharacterFieldStillProtectsDockAndSafeAreas(): void
 {
  $root=dirname(__DIR__,3); $css=file_get_contents($root.'/native/pocket-companion/src/native.css');
  self::assertStringContainsString('env(safe-area-inset-top)',$css);
  self::assertStringContainsString('env(safe-area-inset-bottom)',$css);
  self::assertStringContainsString('.native-dashboard-dock',$css);
 }
}
