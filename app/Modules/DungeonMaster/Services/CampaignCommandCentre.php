<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Services;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Encounter;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\JournalEntry;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Session;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\EncounterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\JournalRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\SessionRepository;

defined('ABSPATH') || exit;

final class CampaignCommandCentre
{
    public function __construct(
        private CampaignRosterRepository $roster,
        private SessionRepository $sessions,
        private EncounterRepository $encounters,
        private JournalRepository $journal
    ) {}

    /** @return array<string,mixed> */
    public function build(Campaign $campaign): array
    {
        $members = $this->roster->members($campaign);
        $sessions = $this->sessions->allForCampaign($campaign);
        $encounters = $this->encounters->allForCampaign($campaign);
        $journal = $this->journal->allForCampaign($campaign);

        return [
            'playerCount' => count($members),
            'characterCount' => array_sum(array_map(
                static fn (array $member): int => count($member['character_ids']),
                $members
            )),
            'sessionCount' => count($sessions),
            'encounterCount' => count($encounters),
            'journalCount' => count($journal),
            'nextSession' => $this->nextSession($sessions),
            'recentSession' => $this->recentSession($sessions),
            'liveEncounter' => $this->firstEncounter($encounters, Encounter::STATUS_RUNNING),
            'preparedEncounter' => $this->firstEncounter($encounters, Encounter::STATUS_PREPARED),
            'pinnedJournal' => array_values(array_slice(array_filter(
                $journal,
                static fn (JournalEntry $entry): bool => $entry->pinned() && $entry->status() !== 'archived'
            ), 0, 3)),
        ];
    }

    /** @param Session[] $sessions */
    private function nextSession(array $sessions): ?Session
    {
        $planned = array_values(array_filter(
            $sessions,
            static fn (Session $session): bool => $session->status() === Session::STATUS_PLANNED
        ));
        usort($planned, static fn (Session $a, Session $b): int => strcmp($a->scheduledDate(), $b->scheduledDate()));
        return $planned[0] ?? null;
    }

    /** @param Session[] $sessions */
    private function recentSession(array $sessions): ?Session
    {
        foreach ($sessions as $session) {
            if ($session->status() === Session::STATUS_PLAYED) {
                return $session;
            }
        }
        return null;
    }

    /** @param Encounter[] $encounters */
    private function firstEncounter(array $encounters, string $status): ?Encounter
    {
        foreach ($encounters as $encounter) {
            if ($encounter->status() === $status) {
                return $encounter;
            }
        }
        return null;
    }
}
