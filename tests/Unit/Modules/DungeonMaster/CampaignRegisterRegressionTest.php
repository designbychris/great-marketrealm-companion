<?php
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;
use PHPUnit\Framework\TestCase;
final class CampaignRegisterRegressionTest extends TestCase
{
 private string $root;
 protected function setUp(): void { parent::setUp();$this->root=dirname(__DIR__,4); }
 public function testCampaignRoutesAndOwnershipRepositoryExist(): void { $routes=file_get_contents($this->root.'/app/Modules/DungeonMaster/Routes.php');$repo=file_get_contents($this->root.'/app/Modules/DungeonMaster/Repositories/CampaignRepository.php');$this->assertStringContainsString("/dungeon-master/campaigns",$routes);$this->assertStringContainsString("'author'=>\$ownerId",str_replace(' ', '', $repo)); }
 public function testCampaignCommandsRequireDmCapability(): void { $request=file_get_contents($this->root.'/app/Modules/DungeonMaster/Requests/StoreCampaignRequest.php');$this->assertStringContainsString("current_user_can('gmrc_manage_campaigns')",$request); }
 public function testCampaignPostTypeIsPrivate(): void { $provider=file_get_contents($this->root.'/app/Modules/DungeonMaster/DungeonMasterServiceProvider.php');$this->assertStringContainsString("'gmrc_campaign'",file_get_contents($this->root.'/app/Modules/DungeonMaster/Repositories/CampaignRepository.php'));$this->assertStringContainsString("'public'=>false",str_replace(' ', '', $provider)); }
 public function testCampaignFormsUseDedicatedNonces(): void { $frontend=file_get_contents($this->root.'/app/Providers/FrontendServiceProvider.php');$this->assertStringContainsString('gmrc_dm_campaign_create',$frontend);$this->assertStringContainsString('gmrc_dm_campaign_',$frontend); }
 public function testCampaignRegisterHasAccessibleResponsiveStyles(): void { $css=file_get_contents($this->root.'/assets/css/modules/dungeon-master/campaign-register.css');$compact=preg_replace('/\s+/', '', $css);$this->assertStringContainsString(':focus-visible',$css);$this->assertStringContainsString('forced-colors:active',$compact);$this->assertStringContainsString('prefers-reduced-transparency:reduce',$compact); }
 public function testDeskOpensCampaignRegister(): void { $view=file_get_contents($this->root.'/app/Modules/DungeonMaster/Views/index.php');$this->assertStringContainsString('dungeon-master/campaigns',$view);$this->assertStringContainsString('Open Campaign Register',$view); }
}
