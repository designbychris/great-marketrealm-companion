<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Dashboard\Services;

use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;

final class GuildHallDirectory
{
    /** @return array<int, array<string, mixed>> */
    public function forAccount(string $accountType, bool $canManageCampaigns): array
    {
        $rooms = $this->commonRooms($accountType);

        if ($accountType === AccountType::PLAYER) {
            array_splice($rooms, 1, 0, $this->playerRooms());
        }

        if ($canManageCampaigns) {
            array_splice($rooms, 1, 0, [$this->dungeonMasterRoom()]);
        }

        $rooms[] = $this->honoursRoom();

        return $rooms;
    }

    /** @return array<int, array<string, mixed>> */
    private function commonRooms(string $accountType): array
    {
        return [
            $this->room('characters', '✒', 'Open now', 'Adventurer Register', 'Open your Character Ledgers, Guild Journal, Leather Satchel and create new adventurers.', [['route' => 'characters', 'label' => 'Open the Register'], ['route' => 'characters/create', 'label' => 'Inscribe an Adventurer']]),
            $this->room('parties', '⚔', 'Your adventuring companies', 'Fellowship Register', 'Open owned and shared Fellowships, their members, treasury, treasure and chronicles.', $this->fellowshipActions($accountType)),
            $this->room('library', '📚', 'Canonical Guild records', 'Guild Library', 'Browse spells, backgrounds, armoury records, relics and the player-safe Field Guide.', [['route' => 'library', 'label' => 'Enter the Library']]),
            $this->room('profile', '✦', 'Your Guild papers', 'Guild Profile', 'Review your Guild identity, adventurers, Campaign relationships and Fellowship memberships.', [['route' => 'guild-profile', 'label' => 'Open Guild Profile']]),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function playerRooms(): array
    {
        return [
            $this->room('active-campaigns', '🗺', 'Your adventuring tables', 'Active Campaigns', 'See joined Campaigns and nominate the adventurer representing you at each active table.', [['route' => 'active-campaigns', 'label' => 'Open Active Campaigns']]),
            $this->room('market-pass', '🎟', 'Campaign invitation', 'Market Pass', 'Redeem a Dungeon Master’s Market Pass to join an active Campaign.', [['route' => 'market-pass', 'label' => 'Redeem a Market Pass']]),
        ];
    }

    /** @return array<string, mixed> */
    private function dungeonMasterRoom(): array
    {
        return $this->room('dungeon-master', '🜲', 'Dungeon Master workspace', 'Dungeon Master’s Desk', 'Open Campaigns, rosters, sessions, encounters, journals and the Monster Ledger.', [['route' => 'dungeon-master', 'label' => 'Open the Desk']]);
    }

    /** @return array<string, mixed> */
    private function honoursRoom(): array
    {
        return $this->room(
            'guild-honours',
            '★',
            'Certified deeds and milestones',
            'Guild Honours',
            'Open the Book of Deeds to review honours certified from your Companion records.',
            [['route' => 'guild-honours', 'label' => 'Open the Book of Deeds']]
        );
    }

    /** @return array<int, array{route:string,label:string}> */
    private function fellowshipActions(string $accountType): array
    {
        $actions = [['route' => 'parties', 'label' => 'Open Fellowships']];

        if ($accountType === AccountType::PLAYER) {
            $actions[] = ['route' => 'fellowship-seal', 'label' => 'Redeem a Fellowship Seal'];
        }

        return $actions;
    }

    /** @param array<int, array{route:string,label:string}> $actions
     *  @return array<string, mixed>
     */
    private function room(string $key, string $symbol, string $eyebrow, string $title, string $description, array $actions): array
    {
        return compact('key', 'symbol', 'eyebrow', 'title', 'description', 'actions') + ['planned' => false];
    }
}
