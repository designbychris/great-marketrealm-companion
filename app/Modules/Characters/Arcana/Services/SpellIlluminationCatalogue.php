<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Arcana\Services;

defined('ABSPATH') || exit;

/**
 * Companion-certified illumination mechanics for spells projected to Tabletop.
 * Tabletop consumes these values; it must never infer radii from prose.
 */
final class SpellIlluminationCatalogue
{
    /** @return array<string,mixed>|null */
    public function forSpell(?string $canonicalKey, string $stableId): ?array
    {
        $key = sanitize_key((string) ($canonicalKey ?: $stableId));

        if ($key !== 'shelfshine') {
            return null;
        }

        return [
            'source' => 'magical',
            'bright_feet' => 20,
            'dim_feet' => 20,
            'total_feet' => 40,
            'duration' => '1 hour',
            'duration_seconds' => 3600,
            'attachment' => 'carried-object',
            'movable' => true,
            'opaque_cover_suppresses' => true,
            'single_active_cast' => true,
        ];
    }
}
