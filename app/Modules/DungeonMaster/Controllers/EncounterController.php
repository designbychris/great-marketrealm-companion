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
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\EncounterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\SessionRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\SaveEncounterRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use RuntimeException;

defined('ABSPATH') || exit;

final class EncounterController
{
    public function __construct(
        private CampaignRepository $campaigns,
        private EncounterRepository $encounters,
        private SessionRepository $sessions,
        private CampaignRosterRepository $rosters,
        private CharacterRepository $characters,
        private DungeonMasterAccess $access,
        private ViewFactory $views,
        private ResponseFactory $responses,
        private FlashStore $flash
    ) {}

    public function index(string $id): string
    {
        $campaign = $this->campaign($id);
        return $this->render('dungeonmaster.encounters.index', $campaign, ['encounters' => $this->encounters->allForCampaign($campaign)]);
    }

    public function create(string $id): string
    {
        $campaign = $this->campaign($id); $this->assertActive($campaign);
        return $this->render('dungeonmaster.encounters.create', $campaign, $this->formData($campaign, null));
    }

    public function store(string $id, SaveEncounterRequest $request): RedirectResponse
    {
        $campaign = $this->campaign($id); $this->assertActive($campaign);
        $encounter = Encounter::create($campaign->id(), $campaign->ownerId(), $request->title(), $this->validSessionId($campaign, $request->sessionId()), $request->status(), $request->threat(), $request->location(), $request->adversaries(), $request->notes(), $this->validCharacterIds($campaign, $request->characterIds()));
        $this->encounters->save($encounter, $campaign);
        $this->flash->success('The Encounter has been pinned to the Board.');
        return $this->responses->redirect($this->showUrl($campaign->id(), $encounter->id()));
    }

    public function show(string $id, string $encounterId): string
    {
        $campaign = $this->campaign($id); $encounter = $this->encounter($encounterId, $campaign);
        return $this->render('dungeonmaster.encounters.show', $campaign, ['encounter' => $encounter, 'session' => $encounter->sessionId() !== '' ? $this->sessions->findForCampaign($encounter->sessionId(), $campaign) : null, 'characters' => $this->charactersForEncounter($encounter, $campaign)]);
    }

    public function edit(string $id, string $encounterId): string
    {
        $campaign = $this->campaign($id); $this->assertActive($campaign); $encounter = $this->encounter($encounterId, $campaign);
        return $this->render('dungeonmaster.encounters.edit', $campaign, $this->formData($campaign, $encounter));
    }

    public function update(string $id, string $encounterId, SaveEncounterRequest $request): RedirectResponse
    {
        $campaign = $this->campaign($id); $this->assertActive($campaign); $encounter = $this->encounter($encounterId, $campaign);
        $encounter->update($request->title(), $this->validSessionId($campaign, $request->sessionId()), $request->status(), $request->threat(), $request->location(), $request->adversaries(), $request->notes(), $this->validCharacterIds($campaign, $request->characterIds()));
        $this->encounters->save($encounter, $campaign);
        $this->flash->success('The Encounter Board entry has been updated.');
        return $this->responses->redirect($this->showUrl($campaign->id(), $encounter->id()));
    }

    private function campaign(string $id): Campaign
    {
        if (! $this->access->allows()) { status_header(403); throw new RuntimeException('The Encounter Board is sealed to Dungeon Masters.'); }
        $campaign = $this->campaigns->findForOwner($id, get_current_user_id());
        if (! $campaign instanceof Campaign) { throw new RuntimeException('Campaign not found in this Dungeon Master’s Register.'); }
        return $campaign;
    }
    private function encounter(string $id, Campaign $campaign): Encounter
    {
        $encounter = $this->encounters->findForCampaign($id, $campaign);
        if (! $encounter instanceof Encounter) { throw new RuntimeException('Encounter not found on this Campaign Board.'); }
        return $encounter;
    }
    private function assertActive(Campaign $campaign): void
    {
        if ($campaign->isArchived()) { throw new RuntimeException('Archived campaigns have a read-only Encounter Board.'); }
    }
    private function validSessionId(Campaign $campaign, string $sessionId): string
    {
        if ($sessionId === '') { return ''; }
        return $this->sessions->findForCampaign($sessionId, $campaign) !== null ? $sessionId : '';
    }
    /** @param array<mixed> $submitted @return string[] */
    private function validCharacterIds(Campaign $campaign, array $submitted): array
    {
        $submitted = array_values(array_unique(array_map('strval', $submitted))); $allowed = [];
        foreach ($this->rosters->members($campaign) as $member) { $allowed = array_merge($allowed, array_map('strval', $member['character_ids'] ?? [])); }
        return array_values(array_intersect($submitted, array_unique($allowed)));
    }
    /** @return array<string,mixed> */
    private function formData(Campaign $campaign, ?Encounter $encounter): array
    {
        $characters = [];
        foreach ($this->rosters->members($campaign) as $member) {
            $ownerId = (int) ($member['player_id'] ?? 0);
            foreach (($member['character_ids'] ?? []) as $characterId) {
                try { $character = $this->characters->findForOwner(CharacterId::fromString((string) $characterId), $ownerId); if ($character !== null) { $characters[] = $character; } } catch (\Throwable) { continue; }
            }
        }
        return ['encounter' => $encounter, 'sessions' => $this->sessions->allForCampaign($campaign), 'characters' => $characters];
    }
    /** @return array<int,mixed> */
    private function charactersForEncounter(Encounter $encounter, Campaign $campaign): array
    {
        $wanted = $encounter->characterIds(); $found = [];
        foreach ($this->rosters->members($campaign) as $member) {
            $ownerId = (int) ($member['player_id'] ?? 0);
            foreach (($member['character_ids'] ?? []) as $characterId) {
                if (! in_array((string) $characterId, $wanted, true)) { continue; }
                try { $character = $this->characters->findForOwner(CharacterId::fromString((string) $characterId), $ownerId); if ($character !== null) { $found[] = $character; } } catch (\Throwable) { continue; }
            }
        }
        return $found;
    }
    /** @param array<string,mixed> $extra */
    private function render(string $view, Campaign $campaign, array $extra): string
    {
        return $this->views->render(View::make($view, array_merge(['campaign' => $campaign, 'flash' => ['success' => $this->flash->get('success'), 'error' => $this->flash->get('error')]], $extra)));
    }
    private function showUrl(string $campaignId, string $encounterId): string
    {
        return add_query_arg('gmrc_route', 'dungeon-master/campaigns/' . $campaignId . '/encounters/' . $encounterId, home_url('/companion/'));
    }
}
