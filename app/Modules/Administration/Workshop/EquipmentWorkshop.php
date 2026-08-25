<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

use RuntimeException;

defined('ABSPATH') || exit;

/** Persistent Steward-authored mundane equipment kept separate from protected Armoury canon. */
final class EquipmentWorkshop
{
    public const OPTION = 'gmrc_steward_equipment';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';
    public const CATEGORIES = ['weapon', 'armour', 'shield', 'gear', 'tool', 'consumable'];
    public const EQUIP_SLOTS = ['main-hand', 'off-hand', 'body'];
    public const DAMAGE_TYPES = ['acid', 'bludgeoning', 'cold', 'fire', 'force', 'lightning', 'necrotic', 'piercing', 'poison', 'psychic', 'radiant', 'slashing', 'thunder'];

    /** @return array<string,array<string,mixed>> */
    public function all(): array
    {
        $records = function_exists('get_option') ? get_option(self::OPTION, []) : [];
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
        $records = $this->all();
        $key = sanitize_key($key);
        return isset($records[$key]) && is_array($records[$key]) ? $records[$key] : null;
    }

    /** @param array<string,mixed> $input */
    public function save(string $key, array $input): string
    {
        $records = $this->all();
        $existing = $key !== '' ? ($records[sanitize_key($key)] ?? null) : null;
        $name = sanitize_text_field((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new RuntimeException('A Steward item requires a name.');
        }

        $key = is_array($existing) ? sanitize_key($key) : $this->uniqueKey($name, $records);
        $status = sanitize_key((string) ($input['status'] ?? self::STATUS_DRAFT));
        if (! in_array($status, [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_ARCHIVED], true)) {
            $status = self::STATUS_DRAFT;
        }

        $category = sanitize_key((string) ($input['category'] ?? ''));
        $description = sanitize_textarea_field((string) ($input['description'] ?? ''));
        $weight = $this->weight($input['weight'] ?? null);
        $slot = $this->allowed((string) ($input['equip_slot'] ?? ''), self::EQUIP_SLOTS);
        $damageDie = $this->damageDie((string) ($input['damage_die'] ?? ''));
        $damageType = $this->allowed((string) ($input['damage_type'] ?? ''), self::DAMAGE_TYPES);
        $armourBase = $this->nullableInt($input['armour_base'] ?? null, 1, 30);
        $dexterityCap = $this->nullableInt($input['dexterity_cap'] ?? null, 0, 10);
        $armourBonus = $this->nullableInt($input['armour_bonus'] ?? null, -10, 10) ?? 0;
        $properties = $this->properties((string) ($input['properties'] ?? ''));
        $range = sanitize_text_field((string) ($input['range'] ?? ''));

        if ($status === self::STATUS_PUBLISHED) {
            $this->assertPublishable($category, $description, $weight, $slot, $damageDie, $damageType, $armourBase, $armourBonus);
        }

        $records[$key] = [
            'key' => $key,
            'origin' => 'steward',
            'status' => $status,
            'name' => $name,
            'category' => $category,
            'description' => $description,
            'weight' => $weight ?? 0.0,
            'provenance' => 'steward-created',
            'equip_slot' => $slot,
            'damage_die' => $damageDie,
            'damage_type' => $damageType,
            'armour_base' => $armourBase,
            'dexterity_cap' => $dexterityCap,
            'armour_bonus' => $armourBonus,
            'properties' => $properties,
            'range' => $range !== '' ? $range : null,
            'steward_notes' => sanitize_textarea_field((string) ($input['steward_notes'] ?? '')),
            'updated_at' => gmdate('c'),
        ];

        update_option(self::OPTION, $records, false);
        return $key;
    }

    private function assertPublishable(string $category, string $description, ?float $weight, ?string $slot, ?string $damageDie, ?string $damageType, ?int $armourBase, int $armourBonus): void
    {
        if (! in_array($category, self::CATEGORIES, true) || $description === '' || $weight === null) {
            throw new RuntimeException('Published Steward items require a recognised category, description and non-negative weight.');
        }
        if ($category === 'weapon' && ($slot === null || $damageDie === null || $damageType === null)) {
            throw new RuntimeException('Published Steward weapons require an equipment slot, valid damage die and damage type.');
        }
        if ($category === 'armour' && ($slot !== 'body' || $armourBase === null)) {
            throw new RuntimeException('Published Steward armour requires the body slot and an armour base.');
        }
        if ($category === 'shield' && ($slot !== 'off-hand' || $armourBonus === 0)) {
            throw new RuntimeException('Published Steward shields require the off-hand slot and a non-zero armour bonus.');
        }
    }

    private function weight(mixed $value): ?float
    {
        if ($value === '' || $value === null || ! is_numeric($value)) return null;
        $weight = (float) $value;
        return $weight >= 0 && $weight <= 1000 ? round($weight, 2) : null;
    }

    private function nullableInt(mixed $value, int $min, int $max): ?int
    {
        if ($value === '' || $value === null || filter_var($value, FILTER_VALIDATE_INT) === false) return null;
        $number = (int) $value;
        return $number >= $min && $number <= $max ? $number : null;
    }

    private function allowed(string $value, array $allowed): ?string
    {
        $value = sanitize_key($value);
        return in_array($value, $allowed, true) ? $value : null;
    }

    private function damageDie(string $value): ?string
    {
        $value = strtolower(trim(sanitize_text_field($value)));
        return preg_match('/^\d+d(?:4|6|8|10|12|20|100)$/', $value) === 1 ? $value : null;
    }

    /** @return array<int,string> */
    private function properties(string $value): array
    {
        $properties = array_filter(array_map('trim', explode(',', strtolower($value))));
        return array_values(array_unique(array_map('sanitize_key', $properties)));
    }

    /** @param array<string,array<string,mixed>> $records */
    private function uniqueKey(string $name, array $records): string
    {
        $base = sanitize_key($name) ?: 'item';
        $key = 'steward-item-' . $base;
        $i = 2;
        $canonical = $this->canonicalIds();
        while (isset($records[$key]) || isset($canonical[$key])) $key = 'steward-item-' . $base . '-' . $i++;
        return $key;
    }

    /** @return array<string,bool> */
    private function canonicalIds(): array
    {
        $source = require GMRC_PATH . 'app/Modules/Library/Armoury/Data/mundane-armoury.php';
        $ids = [];
        foreach ($source as $entry) $ids[sanitize_key((string) ($entry[0] ?? ''))] = true;
        return $ids;
    }
}
