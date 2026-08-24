<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Honours\Services;

use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildMembershipSummary;

defined('ABSPATH') || exit;

/**
 * Projects canonical Guild Honours from already-certified Companion records.
 */
final class BookOfDeeds
{
    public function __construct(
        private GuildMembershipSummary $memberships,
        private GuildHonourRegistry $registry,
        private GuildHonourLedger $ledger
    ) {
    }

    /** @return array<string,mixed> */
    public function forAccount(int $accountId, string $accountType): array
    {
        $summary = $this->memberships->forAccount($accountId, $accountType);
        $eligible = [];
        $certifiable = [];

        foreach ($this->registry->all() as $definition) {
            if (! in_array($accountType, $definition['accountTypes'], true)) {
                continue;
            }

            $eligible[] = $definition;
            if ($this->qualifies($definition, $summary)) {
                $certifiable[] = (string) $definition['key'];
            }
        }

        $awarded = $this->ledger->certify($accountId, $certifiable);
        $entries = [];

        foreach ($eligible as $definition) {
            $key = (string) $definition['key'];
            $entries[] = $definition + [
                'earned' => isset($awarded[$key]),
                'certified_at' => $awarded[$key] ?? '',
            ];
        }

        return [
            'entries' => $entries,
            'earned' => count(array_filter($entries, static fn (array $entry): bool => ! empty($entry['earned']))),
            'total' => count($entries),
        ];
    }

    /** @param array<string,mixed> $definition
     *  @param array<string,int|string> $summary
     */
    private function qualifies(array $definition, array $summary): bool
    {
        $metric = (string) $definition['metric'];
        $threshold = (int) $definition['threshold'];

        $value = match ($metric) {
            'characters' => (int) ($summary['characters'] ?? 0),
            'campaigns' => (int) ($summary['active_campaigns'] ?? 0) + (int) ($summary['archived_campaigns'] ?? 0),
            'archived_campaigns' => (int) ($summary['archived_campaigns'] ?? 0),
            'fellowships' => (int) ($summary['owned_fellowships'] ?? 0) + (int) ($summary['shared_fellowships'] ?? 0),
            default => 0,
        };

        return $value >= $threshold;
    }
}
