<?php
use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\DungeonMasterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\CampaignController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\PlayerRosterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\SessionController;
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
 $router->get('/dungeon-master/campaigns/{id}/players',[PlayerRosterController::class,'index']);
 $router->post('/dungeon-master/campaigns/{id}/players',[PlayerRosterController::class,'store']);
 $router->delete('/dungeon-master/campaigns/{id}/players/{playerId}',[PlayerRosterController::class,'destroy']);
 $router->post('/dungeon-master/campaigns/{id}/players/{playerId}/characters/{characterId}',[PlayerRosterController::class,'attachCharacter']);
 $router->delete('/dungeon-master/campaigns/{id}/players/{playerId}/characters/{characterId}',[PlayerRosterController::class,'detachCharacter']);
 $router->get('/dungeon-master/campaigns/{id}/sessions',[SessionController::class,'index']);
 $router->get('/dungeon-master/campaigns/{id}/sessions/create',[SessionController::class,'create']);
 $router->post('/dungeon-master/campaigns/{id}/sessions',[SessionController::class,'store']);
 $router->get('/dungeon-master/campaigns/{id}/sessions/{sessionId}/edit',[SessionController::class,'edit']);
 $router->get('/dungeon-master/campaigns/{id}/sessions/{sessionId}',[SessionController::class,'show']);
 $router->put('/dungeon-master/campaigns/{id}/sessions/{sessionId}',[SessionController::class,'update']);
};
