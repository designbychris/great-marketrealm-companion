<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Honours\Services;

defined('ABSPATH') || exit;

/**
 * Canonical Character distinctions recognised by the Guild Archivists.
 */
final class CharacterHonourRegistry
{
    /** @return array<int,array<string,mixed>> */
    public function all(): array
    {
        return [
            $this->honour('first-footfall', '✦', 'First Footfall', 'Take your first certified step as a registered adventurer.', 'level', 1),
            $this->honour('calling-answered', '◆', 'Calling Answered', 'Choose the path that shapes your Calling.', 'calling_path', 1),
            $this->honour('seasoned-adventurer', '⚔', 'Seasoned Adventurer', 'Reach 5th level in the Guild Register.', 'level', 5),
            $this->honour('marketrealm-veteran', '♜', 'Marketrealm Veteran', 'Reach 10th level in the Guild Register.', 'level', 10),
            $this->honour('hero-of-the-shelves', '★', 'Hero of the Shelves', 'Reach 15th level in the Guild Register.', 'level', 15),
            $this->honour('legend-of-the-aisles', '♛', 'Legend of the Aisles', 'Reach 20th level in the Guild Register.', 'level', 20),
        ];
    }

    /** @return array<string,mixed> */
    private function honour(
        string $key,
        string $symbol,
        string $title,
        string $description,
        string $metric,
        int $threshold
    ): array {
        return compact('key', 'symbol', 'title', 'description', 'metric', 'threshold');
    }
}
