<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories;

defined('ABSPATH') || exit;

final class CharacterCatalogueRepository
{
    private const OPTION = 'gmrc_character_catalogue';
    private const VERSION = '3.7.1';

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
        return $this->options($this->snapshot()['races'] ?? []);
    }

    /** @return array<string,string> */
    public function classOptions(): array
    {
        return $this->options($this->snapshot()['classes'] ?? []);
    }

    /** @return array<int,array<string,mixed>> */
    public function heritages(): array
    {
        return array_values($this->snapshot()['heritages'] ?? []);
    }

    /** @return array<int,array<string,mixed>> */
    public function subclasses(): array
    {
        return array_values($this->snapshot()['subclasses'] ?? []);
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
        $path = GMRC_PATH . 'resources/catalogue/players-handbook.v1.json';
        $json = is_file($path) ? file_get_contents($path) : false;
        $decoded = is_string($json) ? json_decode($json, true) : null;
        return is_array($decoded) ? $decoded : [
            'version' => self::VERSION,
            'races' => [], 'heritages' => [], 'classes' => [], 'subclasses' => [],
        ];
    }
}
