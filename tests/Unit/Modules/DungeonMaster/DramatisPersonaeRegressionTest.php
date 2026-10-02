<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;
use PHPUnit\Framework\TestCase;
final class DramatisPersonaeRegressionTest extends TestCase
{
 private function source(string $path):string{$value=file_get_contents(dirname(__DIR__,4).'/'.$path);self::assertIsString($value);return $value;}
 public function testPersonaModelKeepsPeopleAndFactionsInCampaignPlanningVocabulary():void{$s=$this->source('app/Modules/DungeonMaster/Models/Persona.php');self::assertStringContainsString("KINDS=['person','faction']",$s);self::assertStringContainsString("VISIBILITIES=['keeper','players']",$s);self::assertStringContainsString('factionId',$s);}
 public function testRepositoryIsPrivateOwnerAndCampaignScoped():void{$r=$this->source('app/Modules/DungeonMaster/Repositories/PersonaRepository.php');self::assertStringContainsString("public const POST_TYPE='gmrc_dm_persona'",$r);self::assertStringContainsString("'author'=>\$campaign->ownerId()",$r);self::assertStringContainsString('META_CAMPAIGN',$r);$p=$this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');self::assertStringContainsString('PersonaRepository::POST_TYPE',$p);self::assertStringContainsString("'public'=>false",$p);}
 public function testControllerProtectsFactionMembershipAndArchives():void{$s=$this->source('app/Modules/DungeonMaster/Controllers/PersonaController.php');self::assertStringContainsString('validFactionId',$s);self::assertStringContainsString('activeMembers',$s);self::assertStringContainsString('Move or archive this faction’s active members',$s);self::assertStringContainsString('Archived campaigns have a read-only Dramatis Personae.',$s);}
 public function testRoutesExposeCampaignScopedRegister():void{$s=$this->source('app/Modules/DungeonMaster/Routes.php');self::assertStringContainsString("/dungeon-master/campaigns/{id}/personae",$s);self::assertStringContainsString('PersonaController::class',$s);}
 public function testCampaignCommandCentreLinksToDramatisPersonae():void{$s=$this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');self::assertStringContainsString("'/personae'",$s);self::assertStringContainsString('Open Dramatis Personae',$s);}
 public function testWorkshopMarksDramatisPersonaeOpen():void{$s=$this->source('app/Modules/DungeonMaster/Services/KeeperWorkspace.php');$start=strpos($s,"'key' => 'dramatis-personae'");self::assertNotFalse($start);$block=substr($s,(int)$start,600);self::assertStringContainsString("'status' => 'open'",$block);}
 public function testViewsKeepBoardAsReferenceRatherThanSourceOfTruth():void{$s=$this->source('app/Modules/DungeonMaster/Views/personae/show.php');self::assertStringContainsString('stable identity',$s);self::assertStringContainsString('without duplicating its campaign record',$s);}
 public function testDedicatedStylesAreRegistered():void{$p=$this->source('app/Providers/FrontendServiceProvider.php');self::assertStringContainsString('gmrc-dramatis-personae',$p);self::assertStringContainsString('dramatis-personae.css',$p);}
}
