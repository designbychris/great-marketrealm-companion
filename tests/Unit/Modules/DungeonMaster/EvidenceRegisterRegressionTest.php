<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;
use PHPUnit\Framework\TestCase;
final class EvidenceRegisterRegressionTest extends TestCase
{
 private function source(string $path):string{$value=file_get_contents(dirname(__DIR__,4).'/'.$path);self::assertIsString($value);return $value;}
 public function testModelOwnsEvidenceThreadsAndCanonicalRelationships():void{$s=$this->source('app/Modules/DungeonMaster/Models/EvidenceRecord.php');self::assertStringContainsString("RECORD_TYPES=['evidence','thread']",$s);self::assertStringContainsString('threadIds',$s);self::assertStringContainsString('locationIds',$s);self::assertStringContainsString('personaIds',$s);}
 public function testRepositoryIsPrivateOwnerAndCampaignScoped():void{$r=$this->source('app/Modules/DungeonMaster/Repositories/EvidenceRepository.php');self::assertStringContainsString("public const POST_TYPE='gmrc_dm_evidence'",$r);self::assertStringContainsString("'author'=>\$campaign->ownerId()",$r);self::assertStringContainsString('META_CAMPAIGN',$r);$p=$this->source('app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');self::assertStringContainsString('EvidenceRepository::POST_TYPE',$p);self::assertStringContainsString("'public'=>false",$p);}
 public function testControllerValidatesCrossRegisterLinksAndThreadIntegrity():void{$s=$this->source('app/Modules/DungeonMaster/Controllers/EvidenceController.php');self::assertStringContainsString('validLinks',$s);self::assertStringContainsString('activeLocations',$s);self::assertStringContainsString('activePersonae',$s);self::assertStringContainsString('Move or archive the evidence attached to this thread',$s);}
 public function testRoutesExposeCampaignScopedEvidenceRegister():void{$s=$this->source('app/Modules/DungeonMaster/Routes.php');self::assertStringContainsString("/dungeon-master/campaigns/{id}/evidence",$s);self::assertStringContainsString('EvidenceController::class',$s);}
 public function testCampaignCommandCentreLinksToEvidenceRegister():void{$s=$this->source('app/Modules/DungeonMaster/Views/campaigns/show.php');self::assertStringContainsString("'/evidence'",$s);self::assertStringContainsString('Open Evidence Register',$s);}
 public function testWorkshopMarksEvidenceRegisterOpen():void{$s=$this->source('app/Modules/DungeonMaster/Services/KeeperWorkspace.php');$start=strpos($s,"'key' => 'evidence-register'");self::assertNotFalse($start);$block=substr($s,(int)$start,650);self::assertStringContainsString("'status' => 'open'",$block);}
 public function testViewsMakeRelationshipsBoardReadyWithoutDuplicatingSourceData():void{$s=$this->source('app/Modules/DungeonMaster/Views/evidence/_form.php');self::assertStringContainsString('canonical campaign links',$s);self::assertStringContainsString('future Conspiracy Board will visualise them rather than copy them',$s);}
 public function testDedicatedStylesAreRegistered():void{$p=$this->source('app/Providers/FrontendServiceProvider.php');self::assertStringContainsString('gmrc-evidence-register',$p);self::assertStringContainsString('evidence-register.css',$p);}
}
