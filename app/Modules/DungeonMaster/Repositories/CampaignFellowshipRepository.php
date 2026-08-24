<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use RuntimeException;

final class CampaignFellowshipRepository
{
    private const META_FELLOWSHIP = '_gmrc_campaign_fellowship';

    public function __construct(
        private CampaignRepository $campaigns,
        private PartyRepository $parties
    ) {
    }

    public function linked(Campaign $campaign): ?Party
    {
        $stored = get_post_meta($this->postId($campaign), self::META_FELLOWSHIP, true);

        if (! is_array($stored)) {
            return null;
        }

        $partyId = (string) ($stored['party_id'] ?? '');
        $ownerId = (int) ($stored['owner_id'] ?? 0);

        if ($partyId === '' || $ownerId < 1) {
            return null;
        }

        try {
            return $this->parties->findForOwner(
                PartyId::fromString($partyId),
                PartyOwnerId::fromInt($ownerId)
            );
        } catch (\Throwable) {
            return null;
        }
    }

    /** @return string[] */
    public function managedCharacterIds(Campaign $campaign): array
    {
        $stored = get_post_meta($this->postId($campaign), self::META_FELLOWSHIP, true);

        if (! is_array($stored) || ! is_array($stored['managed_character_ids'] ?? null)) {
            return [];
        }

        return array_values(array_unique(array_filter(
            array_map('strval', $stored['managed_character_ids']),
            static fn (string $id): bool => trim($id) !== ''
        )));
    }

    /** @param string[] $characterIds */
    public function link(Campaign $campaign, Party $party, array $characterIds = []): void
    {
        update_post_meta(
            $this->postId($campaign),
            self::META_FELLOWSHIP,
            [
                'party_id' => $party->id()->value(),
                'owner_id' => $party->ownerId()->value(),
                'managed_character_ids' => array_values(array_unique($characterIds)),
            ]
        );
    }

    /** @param string[] $characterIds */
    public function setManagedCharacterIds(Campaign $campaign, array $characterIds): void
    {
        $party = $this->linked($campaign);

        if (! $party instanceof Party) {
            return;
        }

        $this->link($campaign, $party, $characterIds);
    }

    public function unlink(Campaign $campaign): void
    {
        delete_post_meta($this->postId($campaign), self::META_FELLOWSHIP);
    }

    private function postId(Campaign $campaign): int
    {
        $postId = $this->campaigns->postIdForOwner(
            $campaign->id(),
            $campaign->ownerId()
        );

        if ($postId === null) {
            throw new RuntimeException('The Campaign Register record could not be found.');
        }

        return $postId;
    }
}
