<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Models\CanonicalMonster;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories\CanonicalBestiary;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Administrator-owned canonical Bestiary overrides.
 *
 * The Dungeon Master Guide remains the immutable baseline. Steward changes are
 * stored separately so an individual creature can always be restored to canon.
 */
final class CanonicalBestiarySteward
{
    public const OPTION = 'gmrc_canonical_bestiary_overrides';

    public function __construct(private CanonicalBestiary $bestiary) {}

    /** @return CanonicalMonster[] */
    public function all(): array
    {
        return $this->bestiary->all();
    }

    public function find(string $key): ?CanonicalMonster
    {
        return $this->bestiary->find($key);
    }

    /** @param array<string,mixed> $input */
    public function save(string $key, array $input): void
    {
        $record = $this->bestiary->find($key);
        if (! $record instanceof CanonicalMonster) {
            throw new RuntimeException('Canonical Bestiary record not found.');
        }

        $attachmentId = absint($input['image_attachment_id'] ?? 0);
        if ($attachmentId > 0 && ! wp_attachment_is_image($attachmentId)) {
            throw new RuntimeException('The selected Bestiary artwork must be a WordPress image attachment.');
        }

        $overrides = $this->overrides();
        $overrides[$record->key()] = [
            'name' => sanitize_text_field((string) ($input['name'] ?? '')),
            'type' => sanitize_text_field((string) ($input['type'] ?? '')),
            'size' => sanitize_text_field((string) ($input['size'] ?? '')),
            'alignment' => sanitize_text_field((string) ($input['alignment'] ?? '')),
            'ac' => $this->nullableInt($input['ac'] ?? null, 0, 40),
            'armor_description' => sanitize_text_field((string) ($input['armor_description'] ?? '')),
            'hp' => $this->nullableInt($input['hp'] ?? null, 0, 9999),
            'hp_formula' => sanitize_text_field((string) ($input['hp_formula'] ?? '')),
            'speed' => sanitize_text_field((string) ($input['speed'] ?? '')),
            'str' => $this->nullableInt($input['str'] ?? null, 1, 30),
            'dex' => $this->nullableInt($input['dex'] ?? null, 1, 30),
            'con' => $this->nullableInt($input['con'] ?? null, 1, 30),
            'int' => $this->nullableInt($input['int'] ?? null, 1, 30),
            'wis' => $this->nullableInt($input['wis'] ?? null, 1, 30),
            'cha' => $this->nullableInt($input['cha'] ?? null, 1, 30),
            'cr' => sanitize_text_field((string) ($input['cr'] ?? '')),
            'description' => sanitize_textarea_field((string) ($input['description'] ?? '')),
            'saving_throws' => sanitize_textarea_field((string) ($input['saving_throws'] ?? '')),
            'skills' => sanitize_textarea_field((string) ($input['skills'] ?? '')),
            'damage_resistances' => sanitize_textarea_field((string) ($input['damage_resistances'] ?? '')),
            'damage_immunities' => sanitize_textarea_field((string) ($input['damage_immunities'] ?? '')),
            'damage_vulnerabilities' => sanitize_textarea_field((string) ($input['damage_vulnerabilities'] ?? '')),
            'condition_immunities' => sanitize_textarea_field((string) ($input['condition_immunities'] ?? '')),
            'senses' => sanitize_textarea_field((string) ($input['senses'] ?? '')),
            'languages' => sanitize_textarea_field((string) ($input['languages'] ?? '')),
            'spellcasting' => sanitize_textarea_field((string) ($input['spellcasting'] ?? '')),
            'reactions' => sanitize_textarea_field((string) ($input['reactions'] ?? '')),
            'legendary_actions' => sanitize_textarea_field((string) ($input['legendary_actions'] ?? '')),
            'mythic_actions' => sanitize_textarea_field((string) ($input['mythic_actions'] ?? '')),
            'lair_actions' => sanitize_textarea_field((string) ($input['lair_actions'] ?? '')),
            'traits' => sanitize_textarea_field((string) ($input['traits'] ?? '')),
            'actions' => sanitize_textarea_field((string) ($input['actions'] ?? '')),
            'notes' => sanitize_textarea_field((string) ($input['notes'] ?? '')),
            'image_attachment_id' => $attachmentId,
        ];

        update_option(self::OPTION, $overrides, false);
        $this->bestiary->flush();
    }

    public function reset(string $key): void
    {
        $record = $this->bestiary->find($key);
        if (! $record instanceof CanonicalMonster) {
            throw new RuntimeException('Canonical Bestiary record not found.');
        }

        $overrides = $this->overrides();
        unset($overrides[$record->key()]);
        update_option(self::OPTION, $overrides, false);
        $this->bestiary->flush();
    }

    public function hasOverride(string $key): bool
    {
        return isset($this->overrides()[sanitize_key($key)]);
    }

    /** @return array<string,array<string,mixed>> */
    private function overrides(): array
    {
        $value = get_option(self::OPTION, []);
        return is_array($value) ? $value : [];
    }

    private function nullableInt(mixed $value, int $min, int $max): ?int
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return max($min, min($max, (int) $value));
    }
}
