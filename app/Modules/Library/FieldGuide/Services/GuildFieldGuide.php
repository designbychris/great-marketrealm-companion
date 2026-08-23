<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\FieldGuide\Services;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Models\CanonicalMonster;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories\CanonicalBestiary;

defined('ABSPATH') || exit;

/**
 * Player-safe projection of the canonical Dungeon Master Bestiary.
 *
 * This service deliberately exposes only fields approved for the Guild Field
 * Guide. Combat statistics and Dungeon Master mechanics never cross this seam.
 */
final class GuildFieldGuide
{
    public function __construct(private CanonicalBestiary $bestiary) {}

    /** @return array<int,array<string,mixed>> */
    public function all(string $query = ''): array
    {
        $query = trim($query);
        $records = [];

        foreach ($this->bestiary->all() as $monster) {
            if (! $monster->fieldGuideVisible()) {
                continue;
            }

            $record = $this->project($monster);
            if ($query !== '' && ! $this->matches($record, $query)) {
                continue;
            }

            $records[] = $record;
        }

        usort(
            $records,
            static fn (array $a, array $b): int => strcasecmp(
                (string) ($a['name'] ?? ''),
                (string) ($b['name'] ?? '')
            )
        );

        return $records;
    }

    /** @return array<string,mixed>|null */
    public function find(string $key): ?array
    {
        $monster = $this->bestiary->find($key);
        if (! $monster instanceof CanonicalMonster || ! $monster->fieldGuideVisible()) {
            return null;
        }

        return $this->project($monster);
    }

    /** @return array<string,mixed> */
    private function project(CanonicalMonster $monster): array
    {
        return [
            'key' => $monster->key(),
            'name' => $monster->name(),
            'creature_type' => $monster->creatureType(),
            'size' => $monster->size(),
            'description' => $monster->playerDescription(),
            'image_attachment_id' => $monster->imageAttachmentId(),
        ];
    }

    /** @param array<string,mixed> $record */
    private function matches(array $record, string $query): bool
    {
        $needle = function_exists('mb_strtolower')
            ? mb_strtolower($query)
            : strtolower($query);
        $haystack = implode(' ', [
            (string) ($record['name'] ?? ''),
            (string) ($record['creature_type'] ?? ''),
            (string) ($record['size'] ?? ''),
            (string) ($record['description'] ?? ''),
        ]);
        $haystack = function_exists('mb_strtolower')
            ? mb_strtolower($haystack)
            : strtolower($haystack);

        return str_contains($haystack, $needle);
    }
}
