<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignFellowshipRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use RuntimeException;

final class CampaignFellowshipService
{
    public function __construct(
        private CampaignRosterRepository $rosters,
        private CampaignFellowshipRepository $links,
        private PartyRepository $parties,
        private CampaignMembershipSynchronizer $membershipSync
    ) {
    }

    public function found(Campaign $campaign): Party
    {
        if ($this->links->linked($campaign) instanceof Party) {
            throw new RuntimeException('This Campaign already has a linked Fellowship.');
        }

        $characterIds = $this->assignedCharacterIds($campaign);

        if ($characterIds === []) {
            throw new RuntimeException(
                'Nominate at least one Campaign adventurer before founding a Fellowship.'
            );
        }

        $party = Party::create(
            PartyId::generate(),
            PartyName::fromString($this->fellowshipName($campaign)),
            PartyOwnerId::fromInt($campaign->ownerId())
        );

        foreach ($characterIds as $characterId) {
            $party->addMember(CharacterId::fromString($characterId));
        }

        $this->parties->save($party);
        $this->links->link($campaign, $party, $characterIds);
        $this->membershipSync->synchronize($campaign);

        return $party;
    }

    public function linkExisting(Campaign $campaign, string $partyId): Party
    {
        $current = $this->links->linked($campaign);

        if ($current instanceof Party) {
            if ($current->id()->value() === $partyId) {
                $this->membershipSync->synchronize($campaign);
                return $this->links->linked($campaign) ?? $current;
            }

            throw new RuntimeException(
                'Release the current Campaign Fellowship before linking another.'
            );
        }

        $party = $this->parties->findForOwner(
            PartyId::fromString($partyId),
            PartyOwnerId::fromInt($campaign->ownerId())
        );

        if (! $party instanceof Party) {
            throw new RuntimeException(
                'That Fellowship is not available in this Dungeon Master’s Guild Register.'
            );
        }

        $this->links->link($campaign, $party);
        $this->membershipSync->synchronize($campaign);

        return $this->links->linked($campaign) ?? $party;
    }

    public function unlink(Campaign $campaign): void
    {
        $this->membershipSync->release($campaign);
    }

    /** @return string[] */
    private function assignedCharacterIds(Campaign $campaign): array
    {
        $ids = [];

        foreach ($this->rosters->members($campaign) as $member) {
            foreach ($member['character_ids'] as $characterId) {
                $ids[] = (string) $characterId;
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }

    private function fellowshipName(Campaign $campaign): string
    {
        $suffix = ' Adventuring Company';
        $limit = 80 - mb_strlen($suffix);
        $name = mb_substr(trim($campaign->name()), 0, max(2, $limit));

        return rtrim($name) . $suffix;
    }
}
