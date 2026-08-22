<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster;
use GreatMarketrealmCompanion\Core\Container;
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
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\SessionRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\EncounterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\MonsterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories\CanonicalBestiary;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
defined('ABSPATH') || exit;
final class DungeonMasterServiceProvider extends ServiceProvider
{
 public function register(): void {
  $this->app->singleton(DungeonMasterAccess::class);$this->app->singleton(CampaignRepository::class);$this->app->singleton(CampaignRosterRepository::class);$this->app->singleton(SessionRepository::class);$this->app->singleton(EncounterRepository::class);$this->app->singleton(MonsterRepository::class);$this->app->singleton(CanonicalBestiary::class);
  $this->app->bind(DungeonMasterController::class,static fn(Container $c): DungeonMasterController=>new DungeonMasterController($c->make(ViewFactory::class),$c->make(DungeonMasterAccess::class)));
  $this->app->bind(CampaignController::class,static fn(Container $c): CampaignController=>new CampaignController($c->make(CampaignRepository::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(PlayerRosterController::class,static fn(Container $c): PlayerRosterController=>new PlayerRosterController($c->make(CampaignRepository::class),$c->make(CampaignRosterRepository::class),$c->make(CharacterRepository::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(SessionController::class,static fn(Container $c): SessionController=>new SessionController($c->make(CampaignRepository::class),$c->make(SessionRepository::class),$c->make(CampaignRosterRepository::class),$c->make(CharacterRepository::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(InitiativeController::class,static fn(Container $c): InitiativeController=>new InitiativeController($c->make(CampaignRepository::class),$c->make(EncounterRepository::class),$c->make(CampaignRosterRepository::class),$c->make(CharacterRepository::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(MonsterController::class,static fn(Container $c): MonsterController=>new MonsterController($c->make(MonsterRepository::class),$c->make(CanonicalBestiary::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
  $this->app->bind(EncounterController::class,static fn(Container $c): EncounterController=>new EncounterController($c->make(CampaignRepository::class),$c->make(EncounterRepository::class),$c->make(SessionRepository::class),$c->make(CampaignRosterRepository::class),$c->make(CharacterRepository::class),$c->make(MonsterRepository::class),$c->make(CanonicalBestiary::class),$c->make(DungeonMasterAccess::class),$c->make(ViewFactory::class),$c->make(ResponseFactory::class),$c->make(FlashStore::class)));
 }
 public function boot(): void { add_action('init',[$this,'registerPostType']); }
 public function registerPostType(): void { register_post_type(CampaignRepository::POST_TYPE,['labels'=>['name'=>'Campaigns','singular_name'=>'Campaign'],'public'=>false,'show_ui'=>false,'show_in_rest'=>false,'supports'=>['title','author'],'capability_type'=>'post','map_meta_cap'=>true]); register_post_type(SessionRepository::POST_TYPE,['labels'=>['name'=>'Sessions','singular_name'=>'Session'],'public'=>false,'show_ui'=>false,'show_in_rest'=>false,'supports'=>['title','author'],'capability_type'=>'post','map_meta_cap'=>true]); register_post_type(EncounterRepository::POST_TYPE,['labels'=>['name'=>'Encounters','singular_name'=>'Encounter'],'public'=>false,'show_ui'=>false,'show_in_rest'=>false,'supports'=>['title','author'],'capability_type'=>'post','map_meta_cap'=>true]); register_post_type(MonsterRepository::POST_TYPE,['labels'=>['name'=>'Monster Ledger','singular_name'=>'Monster'],'public'=>false,'show_ui'=>false,'show_in_rest'=>false,'supports'=>['title','author'],'capability_type'=>'post','map_meta_cap'=>true]); }
}
