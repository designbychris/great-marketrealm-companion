<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster;
use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\Invitations\InviteCodeGenerator;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\CampaignController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\DungeonMasterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\PlayerRosterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\SessionController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\EncounterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\InitiativeController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\MonsterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\JournalController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\MarketPassController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\ActiveCampaignController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\ReadOnlyCharacterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\MarketPassRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\PlayerCampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignFellowshipRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignTabletopLinkRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Integration\TabletopSessionBridge;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\SessionRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\EncounterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\MonsterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\JournalRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories\CanonicalBestiary;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\CampaignCommandCentre;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\CampaignFellowshipService;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\CampaignMembershipSynchronizer;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
defined('ABSPATH') || exit;
final class DungeonMasterServiceProvider extends ServiceProvider
{
 public function register(): void {
  $this->app->singleton(DungeonMasterAccess::class);$this->app->singleton(CampaignCommandCentre::class);$this->app->singleton(InviteCodeGenerator::class);$this->app->singleton(CampaignRepository::class);$this->app->singleton(CampaignRosterRepository::class);$this->app->singleton(PlayerCampaignRepository::class);$this->app->singleton(MarketPassRepository::class);$this->app->singleton(CampaignFellowshipRepository::class);$this->app->singleton(CampaignTabletopLinkRepository::class);$this->app->singleton(TabletopSessionBridge::class);$this->app->singleton(CampaignFellowshipService::class);$this->app->singleton(CampaignMembershipSynchronizer::class);$this->app->singleton(SessionRepository::class);$this->app->singleton(EncounterRepository::class);$this->app->singleton(MonsterRepository::class);$this->app->singleton(CanonicalBestiary::class);$this->app->singleton(JournalRepository::class);
  $this->app->bind(DungeonMasterController::class,static fn(Container $c): DungeonMasterController=>new DungeonMasterController($c->make(ViewFactory::class),$c->make(DungeonMasterAccess::class)));
  $this->app->bind(CampaignController::class,static fn(Container $c): CampaignController=>new CampaignController($c->make(CampaignRepository::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class),$c->make(CampaignCommandCentre::class)));
  $this->app->bind(PlayerRosterController::class,static fn(Container $c): PlayerRosterController=>new PlayerRosterController($c->make(CampaignRepository::class),$c->make(CampaignRosterRepository::class),$c->make(MarketPassRepository::class),$c->make(CampaignFellowshipRepository::class),$c->make(CampaignFellowshipService::class),$c->make(CampaignMembershipSynchronizer::class),$c->make(PartyRepository::class),$c->make(CharacterRepository::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(ActiveCampaignController::class,static fn(Container $c): ActiveCampaignController=>new ActiveCampaignController($c->make(PlayerCampaignRepository::class),$c->make(CampaignRosterRepository::class),$c->make(CampaignFellowshipRepository::class),$c->make(CharacterRepository::class),$c->make(CampaignMembershipSynchronizer::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(ReadOnlyCharacterController::class,static fn(Container $c): ReadOnlyCharacterController=>new ReadOnlyCharacterController($c->make(CampaignRepository::class),$c->make(CampaignRosterRepository::class),$c->make(CharacterRepository::class),$c->make(DungeonMasterAccess::class),$c->make(CharacterController::class)));
  $this->app->bind(MarketPassController::class,static fn(Container $c): MarketPassController=>new MarketPassController($c->make(CampaignRepository::class),$c->make(CampaignRosterRepository::class),$c->make(MarketPassRepository::class),$c->make(DungeonMasterAccess::class),$c->make(CampaignMembershipSynchronizer::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(SessionController::class,static fn(Container $c): SessionController=>new SessionController($c->make(CampaignRepository::class),$c->make(SessionRepository::class),$c->make(CampaignRosterRepository::class),$c->make(CharacterRepository::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(JournalController::class,static fn(Container $c): JournalController=>new JournalController($c->make(CampaignRepository::class),$c->make(JournalRepository::class),$c->make(SessionRepository::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(InitiativeController::class,static fn(Container $c): InitiativeController=>new InitiativeController($c->make(CampaignRepository::class),$c->make(EncounterRepository::class),$c->make(CampaignRosterRepository::class),$c->make(CharacterRepository::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(MonsterController::class,static fn(Container $c): MonsterController=>new MonsterController($c->make(MonsterRepository::class),$c->make(CanonicalBestiary::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(EncounterController::class,static fn(Container $c): EncounterController=>new EncounterController($c->make(CampaignRepository::class),$c->make(EncounterRepository::class),$c->make(SessionRepository::class),$c->make(CampaignRosterRepository::class),$c->make(CharacterRepository::class),$c->make(MonsterRepository::class),$c->make(CanonicalBestiary::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
 }
 public function boot(): void { add_action('init',[$this,'registerPostType']); add_filter('gmrc_tabletop_bestiary_records',[$this,'tabletopBestiaryRecords']); $bridge=$this->app->make(TabletopSessionBridge::class); add_filter('gmrt_companion_campaign_choices',[$bridge,'campaignChoices'],10,2); add_filter('gmrt_companion_campaign_for_table',[$bridge,'campaignForTable'],10,3); add_filter('gmrt_companion_link_campaign',[$bridge,'linkCampaign'],10,4); add_filter('gmrt_companion_sync_table_session',[$bridge,'synchroniseSession'],10,3); }
 /** @param array<int,array<string,mixed>> $records @return array<int,array<string,mixed>> */
 public function tabletopBestiaryRecords(array $records=[]): array { $bestiary=$this->app->make(CanonicalBestiary::class); foreach($bestiary->all() as $monster){ $record=$monster->tabletopBestiaryRecord(); if($record!==[]){ $records[]=$record; } } return $records; }
 public function registerPostType(): void { register_post_type(CampaignRepository::POST_TYPE,['labels'=>['name'=>'Campaigns','singular_name'=>'Campaign'],'public'=>false,'show_ui'=>false,'show_in_rest'=>false,'supports'=>['title','author'],'capability_type'=>'post','map_meta_cap'=>true]); register_post_type(SessionRepository::POST_TYPE,['labels'=>['name'=>'Sessions','singular_name'=>'Session'],'public'=>false,'show_ui'=>false,'show_in_rest'=>false,'supports'=>['title','author'],'capability_type'=>'post','map_meta_cap'=>true]); register_post_type(EncounterRepository::POST_TYPE,['labels'=>['name'=>'Encounters','singular_name'=>'Encounter'],'public'=>false,'show_ui'=>false,'show_in_rest'=>false,'supports'=>['title','author'],'capability_type'=>'post','map_meta_cap'=>true]); register_post_type(JournalRepository::POST_TYPE,['labels'=>['name'=>'Campaign Journal','singular_name'=>'Journal Entry'],'public'=>false,'show_ui'=>false,'show_in_rest'=>false,'supports'=>['title','editor','author'],'capability_type'=>'post','map_meta_cap'=>true]); register_post_type(MonsterRepository::POST_TYPE,['labels'=>['name'=>'Monster Ledger','singular_name'=>'Monster'],'public'=>false,'show_ui'=>false,'show_in_rest'=>false,'supports'=>['title','author'],'capability_type'=>'post','map_meta_cap'=>true]); }
}
