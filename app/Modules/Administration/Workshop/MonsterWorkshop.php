<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Models\CanonicalMonster;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories\CanonicalBestiary;
use RuntimeException;

defined('ABSPATH') || exit;

/** Persistent Steward-authored creatures kept separate from immutable canon. */
final class MonsterWorkshop
{
    public const OPTION = 'gmrc_steward_monsters';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public function __construct(private CanonicalBestiary $bestiary) {}

    /** @return array<string,array<string,mixed>> */
    public function all(): array
    {
        $records = get_option(self::OPTION, []);
        return is_array($records) ? $records : [];
    }

    /** @return array<string,array<string,mixed>> */
    public function published(): array
    {
        return array_filter($this->all(), static fn (array $record): bool => ($record['status'] ?? '') === self::STATUS_PUBLISHED);
    }

    /** @return array<string,mixed>|null */
    public function find(string $key): ?array
    {
        $key = sanitize_key($key);
        $records = $this->all();
        return isset($records[$key]) && is_array($records[$key]) ? $records[$key] : null;
    }

    /** @param array<string,mixed> $input */
    public function save(string $key, array $input): string
    {
        $records = $this->all();
        $existing = $key !== '' ? ($records[sanitize_key($key)] ?? null) : null;
        $name = sanitize_text_field((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new RuntimeException('A Steward creature requires a name.');
        }

        $key = is_array($existing) ? sanitize_key($key) : $this->uniqueKey($name, $records);
        $attachmentId = absint($input['image_attachment_id'] ?? 0);
        if ($attachmentId > 0 && ! wp_attachment_is_image($attachmentId)) {
            throw new RuntimeException('The selected Bestiary artwork must be a WordPress image attachment.');
        }

        $status = sanitize_key((string) ($input['status'] ?? self::STATUS_DRAFT));
        if (! in_array($status, [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_ARCHIVED], true)) {
            $status = self::STATUS_DRAFT;
        }

        $records[$key] = [
            'key' => $key,
            'origin' => 'steward',
            'status' => $status,
            'name' => $name,
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
            'traits' => sanitize_textarea_field((string) ($input['traits'] ?? '')),
            'spellcasting' => sanitize_textarea_field((string) ($input['spellcasting'] ?? '')),
            'actions' => sanitize_textarea_field((string) ($input['actions'] ?? '')),
            'reactions' => sanitize_textarea_field((string) ($input['reactions'] ?? '')),
            'legendary_actions' => sanitize_textarea_field((string) ($input['legendary_actions'] ?? '')),
            'mythic_actions' => sanitize_textarea_field((string) ($input['mythic_actions'] ?? '')),
            'lair_actions' => sanitize_textarea_field((string) ($input['lair_actions'] ?? '')),
            'notes' => sanitize_textarea_field((string) ($input['notes'] ?? '')),
            'image_attachment_id' => $attachmentId,
            'field_guide_visible' => ! empty($input['field_guide_visible']),
            'player_description' => sanitize_textarea_field((string) ($input['player_description'] ?? '')),
            'updated_at' => gmdate('c'),
        ];

        update_option(self::OPTION, $records, false);
        $this->bestiary->flush();
        return $key;
    }

    private function uniqueKey(string $name, array $records): string
    {
        $base = sanitize_key($name) ?: 'steward-creature';
        $key = 'steward-' . $base;
        $suffix = 2;
        while (isset($records[$key]) || $this->bestiary->findCanonical($key) instanceof CanonicalMonster) {
            $key = 'steward-' . $base . '-' . $suffix++;
        }
        return $key;
    }

    private function nullableInt(mixed $value, int $min, int $max): ?int
    {
        if ($value === '' || $value === null) return null;
        return max($min, min($max, (int) $value));
    }
}
