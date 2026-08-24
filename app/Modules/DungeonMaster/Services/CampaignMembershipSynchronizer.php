<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignFellowshipRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use Throwable;

/**
 * Keeps Campaign-owned Fellowship memberships aligned with roster nominations.
 *
 * Only memberships created by this synchronizer are removed automatically.
 * Characters who already belonged to a linked Fellowship remain independent
 * Fellowship members when their Campaign assignment later changes.
 */
final class CampaignMembershipSynchronizer
{
    public function __construct(
        private CampaignRosterRepository $rosters,
        private CampaignFellowshipRepository $links,
        private PartyRepository $parties
    ) {
    }

    public function synchronize(Campaign $campaign): void
    {
        $party = $this->links->linked($campaign);

        if (! $party instanceof Party) {
            return;
        }

        $assigned = array_fill_keys($this->assignedCharacterIds($campaign), true);
        $managed = array_fill_keys($this->links->managedCharacterIds($campaign), true);
        $changed = false;

        foreach (array_keys($managed) as $characterId) {
            if (isset($assigned[$characterId])) {
                continue;
            }

            $id = $this->characterId($characterId);
            if ($id instanceof CharacterId && $party->hasMember($id)) {
                $party->removeMember($id);
                $changed = true;
            }

            unset($managed[$characterId]);
        }

        foreach (array_keys($assigned) as $characterId) {
            $id = $this->characterId($characterId);

            if (! $id instanceof CharacterId || $party->hasMember($id)) {
                continue;
            }

            $party->addMember($id);
            $managed[$characterId] = true;
            $changed = true;
        }

        if ($changed) {
            $this->parties->save($party);
        }

        $this->links->setManagedCharacterIds($campaign, array_keys($managed));
    }

    public function release(Campaign $campaign): void
    {
        $party = $this->links->linked($campaign);

        if (! $party instanceof Party) {
            $this->links->unlink($campaign);
            return;
        }

        $changed = false;

        foreach ($this->links->managedCharacterIds($campaign) as $characterId) {
            $id = $this->characterId($characterId);

            if ($id instanceof CharacterId && $party->hasMember($id)) {
                $party->removeMember($id);
                $changed = true;
            }
        }

        if ($changed) {
            $this->parties->save($party);
        }

        $this->links->unlink($campaign);
    }

    /** @return string[] */
    private function assignedCharacterIds(Campaign $campaign): array
    {
        $ids = [];

        foreach ($this->rosters->members($campaign) as $member) {
            foreach ($member['character_ids'] as $characterId) {
                $characterId = trim((string) $characterId);
                if ($characterId !== '') {
                    $ids[$characterId] = true;
                }
            }
        }

        return array_keys($ids);
    }

    private function characterId(string $value): ?CharacterId
    {
        try {
            return CharacterId::fromString($value);
        } catch (Throwable) {
            return null;
        }
    }
}
