<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;
use PHPUnit\Framework\TestCase;
final class ConspiracyBoardRegressionTest extends TestCase
{
 public function testBoardIsCampaignScopedAndUsesCanonicalRegisters():void{$controller=$this->source('app/Modules/DungeonMaster/Controllers/ConspiracyBoardController.php');self::assertStringContainsString('LocationRepository',$controller);self::assertStringContainsString('PersonaRepository',$controller);self::assertStringContainsString('EvidenceRepository',$controller);self::assertStringContainsString("'/dungeon-master/campaigns/{id}/conspiracy-board'",$this->source('app/Modules/DungeonMaster/Routes.php'));}
 public function testBoardStoresOnlyPresentationPositionsOnCampaign():void{$repo=$this->source('app/Modules/DungeonMaster/Repositories/ConspiracyBoardRepository.php');self::assertStringContainsString('_gmrc_conspiracy_board_positions',$repo);self::assertStringNotContainsString('post_title',$repo);self::assertStringNotContainsString('wp_insert_post',$repo);}
 public function testCampaignCommandCentreOpensBoard():void{$view=$this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');self::assertStringContainsString("\$campaignPath . '/conspiracy-board'",$view);self::assertStringContainsString('Open Conspiracy Board',$view);}
 public function testBoardPresentationHasPinsStringsAndNoVisiblePhaseNumber():void{$view=$this->source('app/Modules/DungeonMaster/Views/conspiracy-board/index.php');self::assertStringContainsString('data-gmrc-board-canvas',$view);self::assertStringContainsString('data-gmrc-board-strings',$view);self::assertStringContainsString('Save Board Layout',$view);self::assertStringNotContainsString('III.17.5',$view);}
 public function testGatewayRecognisesBoardNonce():void{$provider=$this->source('app/Providers/FrontendServiceProvider.php');self::assertStringContainsString('gmrc_dm_conspiracy_board_',$provider);self::assertStringContainsString("/conspiracy-board$#",$provider);}
 private function source(string $path):string{$source=file_get_contents(dirname(__DIR__,4).'/'.$path);self::assertIsString($source);return $source;}
}
