<?php
use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\DungeonMasterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\CampaignController;
defined('ABSPATH') || exit;
return static function (Router $router): void {
 $router->get('/dungeon-master',[DungeonMasterController::class,'index']);
 $router->get('/dungeon-master/campaigns',[CampaignController::class,'index']);
 $router->get('/dungeon-master/campaigns/create',[CampaignController::class,'create']);
 $router->post('/dungeon-master/campaigns',[CampaignController::class,'store']);
 $router->get('/dungeon-master/campaigns/{id}/edit',[CampaignController::class,'edit']);
 $router->get('/dungeon-master/campaigns/{id}',[CampaignController::class,'show']);
 $router->put('/dungeon-master/campaigns/{id}',[CampaignController::class,'update']);
 $router->post('/dungeon-master/campaigns/{id}/archive',[CampaignController::class,'archive']);
};
