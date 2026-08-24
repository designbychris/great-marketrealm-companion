<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\StartingEquipmentPackage;

use RuntimeException;

defined('ABSPATH') || exit;

final class StartingEquipmentPackageRegister
{
    public const OPTION = 'gmrc_starting_equipment_package_overrides';

    /** @return StartingEquipmentPackage[] */
    public function all(): array
    {
        $records = [];
        foreach ($this->source() as $entry) {
            $record = $this->make($entry);
            $records[] = $record;
        }
        return $records;
    }

    /** @return StartingEquipmentPackage[] */
    public function forClass(string $classKey): array
    {
        $classKey = sanitize_key($classKey);
        return array_values(array_filter($this->all(), static fn (StartingEquipmentPackage $package): bool => $package->classKey() === $classKey));
    }

    public function find(string $id): ?StartingEquipmentPackage
    {
        $id = sanitize_key($id);
        foreach ($this->all() as $package) {
            if ($package->id() === $id) { return $package; }
        }
        return null;
    }

    public function defaultForClass(string $classKey): ?StartingEquipmentPackage
    {
        return $this->forClass($classKey)[0] ?? null;
    }

    /** @param array<string,mixed> $input */
    public function save(string $id, array $input): void
    {
        $baseline = $this->baseline($id);
        if ($baseline === null) { throw new RuntimeException('Starting equipment package not found.'); }

        $label = sanitize_text_field((string) ($input['label'] ?? $baseline['label']));
        $raw = preg_split('/[\r\n,]+/', (string) ($input['item_ids'] ?? '')) ?: [];
        $catalogue = new ItemCatalogue();
        $items = [];
        foreach ($raw as $itemId) {
            $itemId = sanitize_key(trim($itemId));
            if ($itemId === '') { continue; }
            if ($catalogue->find($itemId) === null) { throw new RuntimeException('Unknown Armoury item: ' . $itemId); }
            $items[$itemId] = ($items[$itemId] ?? 0) + 1;
        }
        if ($items === []) { throw new RuntimeException('A starting package must contain at least one Armoury item.'); }

        $overrides = $this->overrides();
        $overrides[$id] = ['label' => $label, 'items' => $items];
        if (function_exists('update_option')) { \update_option(self::OPTION, $overrides, false); }
    }

    public function reset(string $id): void
    {
        $overrides = $this->overrides();
        unset($overrides[sanitize_key($id)]);
        if (function_exists('update_option')) { \update_option(self::OPTION, $overrides, false); }
    }

    public function hasOverride(string $id): bool { return isset($this->overrides()[sanitize_key($id)]); }

    /** @return array<string,array<string,mixed>> */
    private function source(): array
    {
        $source = require dirname(__DIR__) . '/Data/starting-equipment-packages.php';
        $overrides = $this->overrides();
        foreach ($source as &$entry) {
            $id = sanitize_key((string) ($entry['id'] ?? ''));
            if (isset($overrides[$id]) && is_array($overrides[$id])) { $entry = array_replace($entry, $overrides[$id]); }
        }
        unset($entry);
        return $source;
    }

    /** @return array<string,mixed>|null */
    private function baseline(string $id): ?array
    {
        $id = sanitize_key($id);
        $source = require dirname(__DIR__) . '/Data/starting-equipment-packages.php';
        foreach ($source as $entry) { if (($entry['id'] ?? '') === $id) { return $entry; } }
        return null;
    }

    /** @param array<string,mixed> $entry */
    private function make(array $entry): StartingEquipmentPackage
    {
        return new StartingEquipmentPackage(
            sanitize_key((string) $entry['id']), sanitize_key((string) $entry['class']),
            (string) $entry['label'], is_array($entry['items'] ?? null) ? $entry['items'] : [],
            (string) ($entry['source'] ?? 'Companion certified starting kit')
        );
    }

    /** @return array<string,array<string,mixed>> */
    private function overrides(): array
    {
        $value = function_exists('get_option') ? \get_option(self::OPTION, []) : [];
        return is_array($value) ? $value : [];
    }
}
