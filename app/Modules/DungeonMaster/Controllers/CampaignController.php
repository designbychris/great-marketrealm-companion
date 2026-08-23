<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;
use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\StoreCampaignRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\UpdateCampaignRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\CampaignCommandCentre;
use RuntimeException;
defined('ABSPATH') || exit;
final class CampaignController
{
 public function __construct(private CampaignRepository $campaigns,private DungeonMasterAccess $access,private ViewFactory $views,private ResponseFactory $responses,private FlashStore $flash,private CampaignCommandCentre $commandCentre){}
 public function index(): string { $this->guard(); return $this->views->render(View::make('dungeonmaster.campaigns.index',['campaigns'=>$this->campaigns->allForOwner($this->ownerId())])); }
 public function create(): string { $this->guard(); return $this->views->render(View::make('dungeonmaster.campaigns.create')); }
 public function store(StoreCampaignRequest $request): RedirectResponse { $this->guard(); $campaign=Campaign::create($request->name(),$this->ownerId(),$request->description());$this->campaigns->save($campaign);$this->flash->success('The campaign has been entered into the Campaign Register.');return $this->responses->redirect($this->url($campaign->id())); }
 public function show(string $id): string { $this->guard(); $campaign=$this->campaign($id); return $this->views->render(View::make('dungeonmaster.campaigns.show',['campaign'=>$campaign,'commandCentre'=>$this->commandCentre->build($campaign)])); }
 public function edit(string $id): string { $this->guard(); return $this->views->render(View::make('dungeonmaster.campaigns.edit',['campaign'=>$this->campaign($id)])); }
 public function update(string $id,UpdateCampaignRequest $request): RedirectResponse { $this->guard();$campaign=$this->campaign($id);$this->assertActive($campaign);$campaign->update($request->name(),$request->description());$this->campaigns->save($campaign);$this->flash->success('The campaign record has been updated.');return $this->responses->redirect($this->url($id)); }
 public function archive(string $id): RedirectResponse { $this->guard();$campaign=$this->campaign($id);$campaign->archive();$this->campaigns->save($campaign);$this->flash->success('The campaign has been archived.');return $this->responses->redirect($this->registerUrl()); }
 private function assertActive(Campaign $campaign): void { if($campaign->isArchived()){throw new RuntimeException('Archived campaigns are preserved as read-only history.');} }
 private function guard(): void { if(!$this->access->allows()){ status_header(403); throw new RuntimeException('This Campaign Register is sealed to Dungeon Masters.'); } }
 private function campaign(string $id): Campaign { $campaign=$this->campaigns->findForOwner($id,$this->ownerId()); if(!$campaign){throw new RuntimeException('Campaign not found in this Dungeon Master’s Register.');} return $campaign; }
 private function ownerId(): int { return get_current_user_id(); }
 private function url(string $id): string { return add_query_arg('gmrc_route','dungeon-master/campaigns/'.$id,home_url('/companion/')); }
 private function registerUrl(): string { return add_query_arg('gmrc_route','dungeon-master/campaigns',home_url('/companion/')); }
}
