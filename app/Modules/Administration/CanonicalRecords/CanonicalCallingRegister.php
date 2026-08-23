<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterCatalogueRepository;

use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Players Handbook-backed Calling and subclass register.
 *
 * The bundled Handbook catalogue is the immutable baseline. Steward changes
 * are stored as an overlay and deliberately do not rewrite character history
 * or progression PHP.
 */
final class CanonicalCallingRegister
{
    public const OPTION = 'gmrc_canonical_calling_overrides';

    public function __construct(private ?CharacterCatalogueRepository $catalogue = null)
    {
        $this->catalogue ??= new CharacterCatalogueRepository();
    }

    /** @return CanonicalCalling[] */
    public function all(): array
    {
        $snapshot = $this->catalogue->snapshot();
        $source = trim((string) ($snapshot['source'] ?? 'Great Marketrealm Players Handbook'));
        $records = [];
        foreach ((array) ($snapshot['classes'] ?? []) as $item) {
            if (is_array($item)) { $record = $this->map($item, 'class', $source); if ($record) { $records[] = $record; } }
        }
        foreach ((array) ($snapshot['subclasses'] ?? []) as $item) {
            if (is_array($item)) { $record = $this->map($item, 'subclass', $source); if ($record) { $records[] = $record; } }
        }
        usort($records, static fn (CanonicalCalling $a, CanonicalCalling $b): int => [$a->kind(), $a->name()] <=> [$b->kind(), $b->name()]);
        return $records;
    }

    public function find(string $kind, string $key): ?CanonicalCalling
    {
        $identity = sanitize_key($kind) . ':' . sanitize_key($key);
        foreach ($this->all() as $record) {
            if ($record->kind() . ':' . $record->key() === $identity) { return $record; }
        }
        return null;
    }

    /** @param array<string,mixed> $input */
    public function save(string $kind, string $key, array $input): void
    {
        $record = $this->find($kind, $key);
        if (! $record) { throw new RuntimeException('Canonical Calling record not found.'); }
        $overrides = $this->overrides();
        $overrides[$this->identity($record)] = [
            'name' => sanitize_text_field((string) ($input['name'] ?? '')),
            'description' => sanitize_textarea_field((string) ($input['description'] ?? '')),
            'steward_notes' => sanitize_textarea_field((string) ($input['steward_notes'] ?? '')),
        ];
        update_option(self::OPTION, $overrides, false);
    }

    public function reset(string $kind, string $key): void
    {
        $record = $this->find($kind, $key);
        if (! $record) { throw new RuntimeException('Canonical Calling record not found.'); }
        $overrides = $this->overrides();
        unset($overrides[$this->identity($record)]);
        update_option(self::OPTION, $overrides, false);
    }

    public function hasOverride(CanonicalCalling $record): bool
    {
        return isset($this->overrides()[$this->identity($record)]);
    }

    /** @param array<string,mixed> $item */
    private function map(array $item, string $kind, string $source): ?CanonicalCalling
    {
        $key = sanitize_key((string) ($item['key'] ?? ''));
        if ($key === '') { return null; }
        $identity = $kind . ':' . $key;
        $override = $this->overrides()[$identity] ?? [];
        $traits = array_values(array_filter(array_map('strval', is_array($item['traits'] ?? null) ? $item['traits'] : [])));
        return new CanonicalCalling(
            $key,
            $kind,
            trim((string) ($override['name'] ?? $item['name'] ?? $key)),
            trim((string) ($override['description'] ?? $item['description'] ?? '')),
            $kind === 'class' && isset($item['hit_die']) ? (int) $item['hit_die'] : null,
            sanitize_key((string) ($item['parent'] ?? '')),
            $traits,
            $source !== '' ? $source : 'Great Marketrealm Players Handbook',
            trim((string) ($override['steward_notes'] ?? ''))
        );
    }

    /** @return array<string,array<string,string>> */
    private function overrides(): array
    {
        $value = get_option(self::OPTION, []);
        return is_array($value) ? $value : [];
    }

    private function identity(CanonicalCalling $record): string
    {
        return $record->kind() . ':' . $record->key();
    }
}
