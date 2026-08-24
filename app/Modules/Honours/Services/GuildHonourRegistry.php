<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Honours\Services;

use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;

defined('ABSPATH') || exit;

/**
 * Canonical register of honours recognised by the Guild Hall.
 */
final class GuildHonourRegistry
{
    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        return [
            $this->honour('first-inscription', '✒', 'First Name in the Ledger', 'Inscribe at least one adventurer in the Guild Register.', 'characters', 1),
            $this->honour('company-of-heroes', '♜', 'A Shelf of Stories', 'Keep three or more adventurers recorded beneath one Guild account.', 'characters', 3),
            $this->honour('campaign-table', '🗺', 'At the Campaign Table', 'Take part in, or steward, at least one Campaign.', 'campaigns', 1),
            $this->honour('fellowship-oath', '⚔', 'Fellowship Forged', 'Belong to or steward at least one Fellowship.', 'fellowships', 1),
            $this->honour('tale-completed', '◆', 'A Tale Entered in the Archives', 'Have at least one Campaign reach the archived record.', 'archived_campaigns', 1),
            $this->honour('campaign-steward', '🜲', 'Keeper of the Campaign Ledger', 'Steward at least one Campaign from the Dungeon Master’s Desk.', 'campaigns', 1, [AccountType::DM]),
        ];
    }

    /** @return array<string, mixed> */
    private function honour(
        string $key,
        string $symbol,
        string $title,
        string $description,
        string $metric,
        int $threshold,
        array $accountTypes = [AccountType::PLAYER, AccountType::DM]
    ): array {
        return compact('key', 'symbol', 'title', 'description', 'metric', 'threshold', 'accountTypes');
    }
}
