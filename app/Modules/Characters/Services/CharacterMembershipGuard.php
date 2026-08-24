<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRosterRepository;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Protects live Campaign and Fellowship relationships from Character deletion.
 */
final class CharacterMembershipGuard
{
    public function __construct(
        private CampaignRepository $campaigns,
        private CampaignRosterRepository $rosters,
        private PartyRepository $parties
    ) {
    }

    public function assertDeletable(CharacterId $characterId): void
    {
        $relationships = $this->liveRelationships($characterId);

        if ($relationships === []) {
            return;
        }

        throw new RuntimeException(sprintf(
            'This adventurer is still recorded in %s. Release them from those active Campaigns or Fellowships before deleting the Character.',
            $this->relationshipList($relationships)
        ));
    }

    /** @return array<int,array{type:string,name:string}> */
    public function liveRelationships(CharacterId $characterId): array
    {
        $relationships = [];
        $needle = $characterId->value();

        foreach ($this->campaigns->all() as $campaign) {
            if (! $campaign instanceof Campaign || $campaign->isArchived()) {
                continue;
            }

            foreach ($this->rosters->members($campaign) as $member) {
                if (in_array($needle, $member['character_ids'], true)) {
                    $relationships[] = [
                        'type' => 'Campaign',
                        'name' => $campaign->name(),
                    ];
                    break;
                }
            }
        }

        foreach ($this->parties->allAcrossOwners() as $party) {
            if (! $party instanceof Party) {
                continue;
            }

            foreach ($party->memberships() as $membership) {
                if ($membership->characterId()->value() === $needle) {
                    $relationships[] = [
                        'type' => 'Fellowship',
                        'name' => $party->name()->value(),
                    ];
                    break;
                }
            }
        }

        return $relationships;
    }

    /** @param array<int,array{type:string,name:string}> $relationships */
    private function relationshipList(array $relationships): string
    {
        return implode(', ', array_map(
            static fn (array $relationship): string => sprintf(
                '%s “%s”',
                $relationship['type'],
                $relationship['name']
            ),
            $relationships
        ));
    }
}
