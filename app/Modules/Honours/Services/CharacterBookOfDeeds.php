<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Honours\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Certifies and projects persistent Character-level Guild distinctions.
 */
final class CharacterBookOfDeeds
{
    public function __construct(
        private CharacterHonourRegistry $registry,
        private CharacterHonourLedger $ledger
    ) {
    }

    /** @return array<string,mixed> */
    public function forCharacter(Character $character, int $accountId): array
    {
        $definitions = $this->registry->all();
        $certifiable = [];

        foreach ($definitions as $definition) {
            if ($this->qualifies($definition, $character)) {
                $certifiable[] = (string) $definition['key'];
            }
        }

        $awarded = $this->ledger->certify(
            $accountId,
            $character->id(),
            $certifiable
        );
        $entries = [];

        foreach ($definitions as $definition) {
            $key = (string) $definition['key'];
            $entries[] = $definition + [
                'earned' => isset($awarded[$key]),
                'certified_at' => $awarded[$key] ?? '',
            ];
        }

        return [
            'entries' => $entries,
            'earned' => count(array_filter(
                $entries,
                static fn (array $entry): bool => ! empty($entry['earned'])
            )),
            'total' => count($entries),
        ];
    }

    /** @param array<string,mixed> $definition */
    private function qualifies(array $definition, Character $character): bool
    {
        $threshold = (int) ($definition['threshold'] ?? 1);

        $value = match ((string) ($definition['metric'] ?? '')) {
            'level' => $character->level()->value(),
            'calling_path' => $character->callingPath()->isChosen() ? 1 : 0,
            default => 0,
        };

        return $value >= $threshold;
    }
}
