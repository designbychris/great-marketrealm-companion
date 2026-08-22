<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Session;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\SessionRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Requests\SaveSessionRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use RuntimeException;
use WP_User;

defined('ABSPATH') || exit;

final class SessionController
{
    public function __construct(
        private CampaignRepository $campaigns,
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
        return $this->render('dungeonmaster.sessions.index', $campaign, [
            'sessions' => $this->sessions->allForCampaign($campaign),
        ]);
    }

    public function create(string $id): string
    {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        return $this->render('dungeonmaster.sessions.create', $campaign, [
            'session' => null,
            'roster' => $this->rosterData($campaign),
        ]);
    }

    public function store(string $id, SaveSessionRequest $request): RedirectResponse
    {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        $session = Session::create(
            $campaign->id(),
            $campaign->ownerId(),
            $request->number(),
            $request->title(),
            $request->scheduledDate(),
            $request->status(),
            $request->prepNotes(),
            $request->recap(),
            $this->attendance($campaign, $request)
        );
        $this->sessions->save($session, $campaign);
        $this->flash->success('The Session has been entered into the Ledger.');
        return $this->responses->redirect($this->showUrl($campaign->id(), $session->id()));
    }

    public function show(string $id, string $sessionId): string
    {
        $campaign = $this->campaign($id);
        $session = $this->session($sessionId, $campaign);
        return $this->render('dungeonmaster.sessions.show', $campaign, [
            'session' => $session,
            'attendance' => $this->attendanceData($session),
        ]);
    }

    public function edit(string $id, string $sessionId): string
    {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        return $this->render('dungeonmaster.sessions.edit', $campaign, [
            'session' => $this->session($sessionId, $campaign),
            'roster' => $this->rosterData($campaign),
        ]);
    }

    public function update(string $id, string $sessionId, SaveSessionRequest $request): RedirectResponse
    {
        $campaign = $this->campaign($id);
        $this->assertActive($campaign);
        $session = $this->session($sessionId, $campaign);
        $session->update(
            $request->number(),
            $request->title(),
            $request->scheduledDate(),
            $request->status(),
            $request->prepNotes(),
            $request->recap(),
            $this->attendance($campaign, $request)
        );
        $this->sessions->save($session, $campaign);
        $this->flash->success('The Session Ledger entry has been updated.');
        return $this->responses->redirect($this->showUrl($campaign->id(), $session->id()));
    }

    private function campaign(string $id): Campaign
    {
        if (! $this->access->allows()) {
            status_header(403);
            throw new RuntimeException('The Session Ledger is sealed to Dungeon Masters.');
        }
        $campaign = $this->campaigns->findForOwner($id, get_current_user_id());
        if (! $campaign instanceof Campaign) {
            throw new RuntimeException('Campaign not found in this Dungeon Master’s Register.');
        }
        return $campaign;
    }

    private function session(string $sessionId, Campaign $campaign): Session
    {
        $session = $this->sessions->findForCampaign($sessionId, $campaign);
        if (! $session instanceof Session) {
            throw new RuntimeException('Session not found in this Campaign Ledger.');
        }
        return $session;
    }

    private function assertActive(Campaign $campaign): void
    {
        if ($campaign->isArchived()) {
            throw new RuntimeException('Archived campaigns have a read-only Session Ledger.');
        }
    }

    /** @return array<int,array{player_id:int,character_ids:array<int,string>}> */
    private function attendance(Campaign $campaign, SaveSessionRequest $request): array
    {
        $selectedPlayers = array_map('intval', $request->playerIds());
        $selectedCharacters = array_map('strval', $request->characterIds());
        $attendance = [];
        foreach ($this->rosters->members($campaign) as $member) {
            $playerId = (int) $member['player_id'];
            if (! in_array($playerId, $selectedPlayers, true)) {
                continue;
            }
            $allowed = array_map('strval', $member['character_ids']);
            $attendance[] = [
                'player_id' => $playerId,
                'character_ids' => array_values(array_intersect($selectedCharacters, $allowed)),
            ];
        }
        return $attendance;
    }

    /** @return array<int,array<string,mixed>> */
    private function rosterData(Campaign $campaign): array
    {
        $rows = [];
        foreach ($this->rosters->members($campaign) as $member) {
            $playerId = (int) $member['player_id'];
            $user = get_userdata($playerId);
            if (! $user instanceof WP_User) {
                continue;
            }
            $linkedIds = array_map('strval', $member['character_ids']);
            $rows[] = [
                'user' => $user,
                'portrait_id' => GuildProfile::portraitAttachmentId($playerId),
                'characters' => array_values(array_filter(
                    $this->characters->allForOwner($playerId),
                    static fn ($character): bool => in_array($character->id()->value(), $linkedIds, true)
                )),
            ];
        }
        return $rows;
    }

    /** @return array<int,array<string,mixed>> */
    private function attendanceData(Session $session): array
    {
        $rows = [];
        foreach ($session->attendance() as $entry) {
            $playerId = (int) ($entry['player_id'] ?? 0);
            $user = get_userdata($playerId);
            if (! $user instanceof WP_User) {
                continue;
            }
            $characters = [];
            foreach (($entry['character_ids'] ?? []) as $characterId) {
                try {
                    $character = $this->characters->findForOwner(
                        \GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId::fromString((string) $characterId),
                        $playerId
                    );
                    if ($character !== null) { $characters[] = $character; }
                } catch (\Throwable) {
                    continue;
                }
            }
            $rows[] = ['user' => $user, 'characters' => $characters];
        }
        return $rows;
    }

    /** @param array<string,mixed> $extra */
    private function render(string $view, Campaign $campaign, array $extra): string
    {
        return $this->views->render(View::make($view, array_merge([
            'campaign' => $campaign,
            'flash' => ['success' => $this->flash->get('success'), 'error' => $this->flash->get('error')],
        ], $extra)));
    }

    private function showUrl(string $campaignId, string $sessionId): string
    {
        return add_query_arg('gmrc_route', 'dungeon-master/campaigns/' . $campaignId . '/sessions/' . $sessionId, home_url('/companion/'));
    }
}
