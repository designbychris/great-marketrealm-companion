<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Encounter;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\InitiativeTable;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\EncounterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\SaveInitiativeRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use RuntimeException;

defined('ABSPATH') || exit;

final class InitiativeController
{
    public function __construct(private CampaignRepository $campaigns, private EncounterRepository $encounters, private CampaignRosterRepository $rosters, private CharacterRepository $characters, private DungeonMasterAccess $access, private ViewFactory $views, private ResponseFactory $responses, private FlashStore $flash) {}

    public function index(string $id, string $encounterId): string
    {
        $campaign=$this->campaign($id); $encounter=$this->encounter($encounterId,$campaign); $table=$this->table($encounter,$campaign);
        return $this->views->render(View::make('dungeonmaster.initiative.index',['campaign'=>$campaign,'encounter'=>$encounter,'table'=>$table,'flash'=>['success'=>$this->flash->get('success'),'error'=>$this->flash->get('error')]]));
    }

    public function update(string $id, string $encounterId, SaveInitiativeRequest $request): RedirectResponse
    {
        $campaign=$this->campaign($id); if($campaign->isArchived()){throw new RuntimeException('Archived campaigns have a read-only Initiative Table.');} $encounter=$this->encounter($encounterId,$campaign);
        $current=$this->table($encounter,$campaign); $combatants=$this->sanitiseCombatants($request->combatants(),$current); $round=$request->round(); $turn=$request->turnIndex(); $action=$request->action();
        if($action==='reset'){ $combatants=$this->seedCombatants($encounter,$campaign); $round=1; $turn=0; }
        if($action==='sort'){ usort($combatants,static fn(array $a,array $b):int=>((int)$b['initiative']<=> (int)$a['initiative']) ?: strcmp((string)$a['name'],(string)$b['name'])); $turn=0; }
        if($action==='advance' && $combatants!==[]){ $turn++; if($turn>=count($combatants)){ $turn=0; $round++; } }
        if($action==='complete'){ $encounter->update($encounter->title(),$encounter->sessionId(),Encounter::STATUS_COMPLETED,$encounter->threat(),$encounter->location(),$encounter->adversaries(),$encounter->notes(),$encounter->characterIds()); $this->encounters->save($encounter,$campaign); }
        elseif($encounter->status()===Encounter::STATUS_PREPARED){ $encounter->update($encounter->title(),$encounter->sessionId(),Encounter::STATUS_RUNNING,$encounter->threat(),$encounter->location(),$encounter->adversaries(),$encounter->notes(),$encounter->characterIds()); $this->encounters->save($encounter,$campaign); }
        $this->encounters->saveInitiative($encounter->id(),$campaign,['round'=>$round,'turn_index'=>$turn,'combatants'=>$combatants]);
        $this->flash->success($action==='complete'?'The Encounter has been completed.':'The Initiative Table has been updated.');
        return $this->responses->redirect($this->url($campaign->id(),$encounter->id()));
    }

    private function table(Encounter $encounter, Campaign $campaign): InitiativeTable
    { $state=$this->encounters->initiativeForCampaign($encounter->id(),$campaign); return $state===[]?InitiativeTable::fresh($this->seedCombatants($encounter,$campaign)):InitiativeTable::restore((int)($state['round']??1),(int)($state['turn_index']??0),is_array($state['combatants']??null)?$state['combatants']:[]); }

    /** @return array<int,array<string,mixed>> */
    private function seedCombatants(Encounter $encounter, Campaign $campaign): array
    {
        $out=[]; $wanted=$encounter->characterIds();
        foreach($this->rosters->members($campaign) as $member){$owner=(int)($member['player_id']??0);foreach(($member['character_ids']??[]) as $cid){$cid=(string)$cid;if(!in_array($cid,$wanted,true)){continue;}try{$c=$this->characters->findForOwner(CharacterId::fromString($cid),$owner);if($c!==null){$hp=$c->hitPoints();$out[]=['id'=>'pc-'.$cid,'type'=>'character','source_id'=>$cid,'name'=>$c->name()->value(),'initiative'=>0,'modifier'=>$c->initiative()->value(),'current_hp'=>$hp->current(),'max_hp'=>$hp->maximum(),'conditions'=>'','defeated'=>false];}}catch(\Throwable){continue;}}}
        $lines=preg_split('/\R+/',trim($encounter->adversaries()))?:[]; foreach($lines as $i=>$line){$name=trim($line);if($name===''){continue;}$out[]=['id'=>'foe-'.substr(sha1($name.'-'.$i),0,12),'type'=>'adversary','source_id'=>'','name'=>$name,'initiative'=>0,'modifier'=>0,'current_hp'=>0,'max_hp'=>0,'conditions'=>'','defeated'=>false];}
        return $out;
    }

    /** @param array<mixed> $submitted @return array<int,array<string,mixed>> */
    private function sanitiseCombatants(array $submitted, InitiativeTable $current): array
    {
        $allowed=[];foreach($current->combatants() as $c){$allowed[(string)($c['id']??'')]=$c;} $out=[];
        foreach($submitted as $row){if(!is_array($row)){continue;}$id=sanitize_text_field((string)($row['id']??''));if(!isset($allowed[$id])){continue;}$base=$allowed[$id];$base['initiative']=max(-20,min(99,(int)($row['initiative']??0)));$base['current_hp']=max(0,min(99999,(int)($row['current_hp']??0)));$base['max_hp']=max(0,min(99999,(int)($row['max_hp']??0)));$base['conditions']=sanitize_text_field((string)($row['conditions']??''));$base['defeated']=!empty($row['defeated']);$out[]=$base;}
        return $out;
    }
    private function campaign(string $id): Campaign {if(!$this->access->allows()){status_header(403);throw new RuntimeException('The Initiative Table is sealed to Dungeon Masters.');}$c=$this->campaigns->findForOwner($id,get_current_user_id());if(!$c instanceof Campaign){throw new RuntimeException('Campaign not found in this Dungeon Master’s Register.');}return $c;}
    private function encounter(string $id,Campaign $campaign): Encounter {$e=$this->encounters->findForCampaign($id,$campaign);if(!$e instanceof Encounter){throw new RuntimeException('Encounter not found on this Campaign Board.');}return $e;}
    private function url(string $campaignId,string $encounterId): string {return add_query_arg('gmrc_route','dungeon-master/campaigns/'.$campaignId.'/encounters/'.$encounterId.'/initiative',home_url('/companion/'));}
}
