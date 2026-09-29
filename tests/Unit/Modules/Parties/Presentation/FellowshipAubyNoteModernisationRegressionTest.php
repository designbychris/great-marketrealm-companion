<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
final class FellowshipAubyNoteModernisationRegressionTest extends TestCase
{
 public function testFellowshipPagesUseCanonicalAnimatedAubyParchment(): void
 {
  $root=dirname(__DIR__,5);
  foreach(['index.php','create.php','show.php'] as $name){
   $source=file_get_contents($root.'/app/Modules/Parties/Views/'.$name);
   self::assertStringContainsString("'components.furniture.auby-note'",$source);
   self::assertStringContainsString('GreatMarketrealmCompanion\\Services\\Auby\\Quote',$source);
   self::assertStringNotContainsString('gmrc-fellowship-auby-note',$source);
   self::assertStringNotContainsString('auby-note-face.svg',$source);
   self::assertStringNotContainsString('🍆',$source);
  }
 }
 public function testFellowshipRegisterKeepsEstablishedAubyLine(): void
 {
  $root=dirname(__DIR__,5);
  $source=file_get_contents($root.'/app/Modules/Parties/Views/index.php');
  self::assertStringContainsString('One adventurer is a record. Several adventurers with snacks are a Fellowship.',$source);
 }
}
