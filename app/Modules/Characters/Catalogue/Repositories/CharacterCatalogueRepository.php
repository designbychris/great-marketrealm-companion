<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\HeritageGuidance;

defined('ABSPATH') || exit;

final class CharacterCatalogueRepository
{
    private const OPTION = 'gmrc_character_catalogue';
    private const VERSION = '3.7.6';

    /** @return array<string,mixed> */
    public function snapshot(): array
    {
        $stored = function_exists('get_option')
            ? get_option(self::OPTION, [])
            : [];

        if ($this->valid($stored)) {
            return $stored;
        }

        $snapshot = $this->bundledSnapshot();

        if (function_exists('update_option')) {
            update_option(self::OPTION, $snapshot, false);
        }

        return $snapshot;
    }

    public function seed(): void
    {
        $this->snapshot();
    }

    /** @return array<string,string> */
    public function raceOptions(): array
    {
        $options = $this->options($this->snapshot()['races'] ?? []);
        if (function_exists('get_option')) {
            $records = get_option('gmrc_steward_folk', []);
            foreach (is_array($records) ? $records : [] as $key => $record) {
                if (is_array($record) && ($record['status'] ?? '') === 'published' && ! isset($options[$key])) {
                    $options[sanitize_key((string) $key)] = (string) ($record['name'] ?? $key);
                }
            }
        }
        return $options;
    }

    /** @return array<string,string> */
    public function classOptions(): array
    {
        $options = $this->options(
            $this->snapshot()['classes'] ?? []
        );

        /*
         * Legacy specialties remain valid identities for
         * existing Characters, but are not top-level Callings.
         */
        unset(
            $options['grocer'],
            $options['cleaver-saint']
        );

        if (function_exists('get_option')) {
            $records = get_option('gmrc_steward_callings', []);
            foreach (is_array($records) ? $records : [] as $key => $record) {
                if (is_array($record) && ($record['status'] ?? '') === 'published' && ! isset($options[$key])) {
                    $options[sanitize_key((string) $key)] = (string) ($record['name'] ?? $key);
                }
            }
        }
        return $options;
    }

    /** @return array<int,array<string,mixed>> */
    public function heritages(): array
    {
        $snapshot = $this->snapshot();
        $parents = [];
        foreach ((array) ($snapshot['races'] ?? []) as $race) {
            if (is_array($race) && is_string($race['key'] ?? null)) {
                $parents[$race['key']] = $race;
            }
        }

        $items = [];
        foreach ((array) ($snapshot['heritages'] ?? []) as $heritage) {
            if (! is_array($heritage)) {
                continue;
            }
            $parent = $parents[(string) ($heritage['parent'] ?? '')] ?? [];
            $items[] = $this->withHeritageGuidance($heritage, $parent);
        }

        if (function_exists('get_option')) {
            $records = get_option('gmrc_steward_folk', []);
            foreach (is_array($records) ? $records : [] as $record) {
                if (! is_array($record) || ($record['status'] ?? '') !== 'published') {
                    continue;
                }
                foreach ((array) ($record['heritages'] ?? []) as $heritage) {
                    if (is_array($heritage)) {
                        $items[] = $this->withHeritageGuidance($heritage, $record);
                    }
                }
            }
        }

        return $items;
    }

    /** @param array<string,mixed> $heritage @param array<string,mixed> $parent */
    private function withHeritageGuidance(array $heritage, array $parent): array
    {
        $heritage['mechanics'] = HeritageGuidance::normalize($heritage);
        $heritage['traits'] = HeritageGuidance::traits($heritage);
        $heritage['parent_name'] = (string) ($parent['name'] ?? '');
        $heritage['parent_description'] = (string) ($parent['description'] ?? '');
        $heritage['parent_traits'] = HeritageGuidance::traits($parent);
        $heritage['parent_mechanics'] = HeritageGuidance::normalize($parent);

        return $heritage;
    }

    /** @return array<int,array<string,mixed>> */
    public function subclasses(): array
    {
        $items = array_values($this->snapshot()['subclasses'] ?? []);
        if (function_exists('get_option')) {
            $records = get_option('gmrc_steward_callings', []);
            foreach (is_array($records) ? $records : [] as $record) {
                if (! is_array($record) || ($record['status'] ?? '') !== 'published') continue;
                foreach ((array) ($record['paths'] ?? []) as $path) if (is_array($path)) $items[] = $path;
            }
        }
        return $items;
    }

    public function heritageBelongsTo(string $heritage, string $race): bool
    {
        if ($heritage === '') {
            return true;
        }

        return $this->belongsTo($this->heritages(), $heritage, $race);
    }

    public function subclassBelongsTo(string $subclass, string $class): bool
    {
        if ($subclass === '') {
            return true;
        }

        return $this->belongsTo($this->subclasses(), $subclass, $class);
    }

    /** @param array<int,array<string,mixed>> $items */
    private function belongsTo(array $items, string $key, string $parent): bool
    {
        foreach ($items as $item) {
            if (($item['key'] ?? '') === $key && ($item['parent'] ?? '') === $parent) {
                return true;
            }
        }

        return false;
    }

    /** @param array<int,array<string,mixed>> $items @return array<string,string> */
    private function options(array $items): array
    {
        $options = [];
        foreach ($items as $item) {
            $key = is_string($item['key'] ?? null) ? $item['key'] : '';
            $name = is_string($item['name'] ?? null) ? $item['name'] : '';
            if ($key !== '' && $name !== '') { $options[$key] = $name; }
        }
        return $options;
    }

    private function valid(mixed $snapshot): bool
    {
        return is_array($snapshot)
            && ($snapshot['version'] ?? null) === self::VERSION
            && is_array($snapshot['races'] ?? null)
            && is_array($snapshot['classes'] ?? null);
    }

    /** @return array<string,mixed> */
    private function bundledSnapshot(): array
    {
        $root = defined('GMRC_PATH')
            ? GMRC_PATH
            : dirname(__DIR__, 5) . DIRECTORY_SEPARATOR;

        $path = $root . 'resources/catalogue/players-handbook.v1.json';
        $json = is_file($path) ? file_get_contents($path) : false;
        $decoded = is_string($json) ? json_decode($json, true) : null;
        return is_array($decoded) ? $decoded : [
            'version' => self::VERSION,
            'races' => [], 'heritages' => [], 'classes' => [], 'subclasses' => [],
        ];
    }
}
