<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Integration\Expansions;

use Closure;
use Throwable;

defined('ABSPATH') || exit;

/**
 * Read-only Companion adapter for active Great MarketRealm Almanac content.
 *
 * GMREXP remains the canonical owner of expansion definitions. This adapter
 * deliberately performs no persistence and returns an empty catalogue when
 * the Expansions plugin/API is unavailable or incompatible.
 */
final class ExpansionCharacterCatalogue
{
    public const MINIMUM_ACTIVE_CONTENT_API_VERSION = '1.0.0';

    /** @var Closure():mixed|null */
    private ?Closure $resolver;

    /**
     * @param Closure():mixed|null $resolver Test seam for an ActiveContentCatalogue-like object.
     */
    public function __construct(?Closure $resolver = null)
    {
        $this->resolver = $resolver;
    }

    public function available(): bool
    {
        return $this->activeCatalogue() !== null;
    }

    /**
     * @return array<string,array<string,mixed>> keyed by Companion-safe local identity.
     */
    public function races(): array
    {
        return $this->definitions('race');
    }

    /**
     * @return array<string,array<string,mixed>> keyed by Companion-safe local identity.
     */
    public function backgrounds(): array
    {
        return $this->definitions('background');
    }

    /**
     * @return array<string,array<string,mixed>> keyed by Companion-safe local identity.
     */
    public function subclasses(): array
    {
        return $this->definitions('subclass');
    }

    /**
     * Return the active Almanac keys that currently expose Character-facing
     * content understood by the Companion. This deliberately bypasses the
     * current-player sharing scope so a Dungeon Master can choose which books
     * to place on a Campaign shelf.
     *
     * @return string[]
     */
    public function activeExpansionKeys(): array
    {
        $keys = [];

        foreach (['race', 'background', 'subclass'] as $type) {
            foreach ($this->unscopedDefinitions($type) as $definition) {
                $expansion = $this->normaliseKey(
                    (string) ($definition['expansion'] ?? '')
                );

                if ($expansion !== '') {
                    $keys[$expansion] = true;
                }
            }
        }

        $keys = array_keys($keys);
        sort($keys);

        return $keys;
    }

    /**
     * Presentation/provenance map for the currently consumable expansion
     * choices. Views can identify expansion cards without owning sourcebook
     * mechanics or hard-coding canonical IDs.
     *
     * @return array<string,array<string,array{expansion:string,label:string,canonical_id:string}>>
     */
    public function presentationMap(): array
    {
        $map = [
            'race' => [],
            'background' => [],
            'subclass' => [],
        ];

        foreach (array_keys($map) as $type) {
            foreach ($this->definitions($type) as $key => $definition) {
                $expansion = $this->normaliseKey(
                    (string) ($definition['expansion'] ?? '')
                );

                if ($expansion === '') {
                    continue;
                }

                $map[$type][$key] = [
                    'expansion' => $expansion,
                    'label' => $this->expansionLabel($expansion),
                    'canonical_id' => (string) ($definition['canonical_id'] ?? ''),
                ];
            }
        }

        return $map;
    }

    /**
     * Resolve the fully-qualified GMREXP identity for an unambiguous active
     * definition. Returns null rather than guessing across colliding packs.
     */
    public function canonicalId(string $type, string $key): ?string
    {
        $definition = $this->definition($type, $key);
        $id = is_array($definition) ? ($definition['canonical_id'] ?? null) : null;

        return is_string($id) && $id !== '' ? $id : null;
    }

    /** @return array<string,mixed>|null */
    public function definition(string $type, string $key): ?array
    {
        $key = $this->normaliseKey($key);
        if ($key === '') {
            return null;
        }

        return $this->definitions($type)[$key] ?? null;
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    private function definitions(string $type): array
    {
        $definitions = $this->unscopedDefinitions($type);
        $scope = $this->currentExpansionScope();

        if ($scope === null) {
            return $definitions;
        }

        return array_filter(
            $definitions,
            static fn (array $definition): bool => in_array(
                (string) ($definition['expansion'] ?? ''),
                $scope,
                true
            )
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    private function unscopedDefinitions(string $type): array
    {
        $catalogue = $this->activeCatalogue();
        if ($catalogue === null || ! method_exists($catalogue, 'ofType')) {
            return [];
        }

        try {
            $entries = $catalogue->ofType(strtolower(trim($type)));
        } catch (Throwable) {
            return [];
        }

        if (! is_array($entries)) {
            return [];
        }

        /*
         * Companion currently stores race/background/subclass local keys in
         * established domain fields. If two active Almanacs expose the same
         * local key we refuse to choose between them. The fully-qualified
         * GMREXP identity is retained in every accepted projection.
         */
        $seen = [];
        $ambiguous = [];
        foreach ($entries as $entry) {
            $projection = $this->projectEntry($entry, $type);
            if ($projection === null) {
                continue;
            }

            $key = (string) $projection['key'];
            if (isset($seen[$key])) {
                unset($seen[$key]);
                $ambiguous[$key] = true;
                continue;
            }
            if (isset($ambiguous[$key])) {
                continue;
            }

            $seen[$key] = $projection;
        }

        ksort($seen);
        return $seen;
    }

    /** @return array<string,mixed>|null */
    private function projectEntry(mixed $entry, string $expectedType): ?array
    {
        if (! is_object($entry)) {
            return null;
        }

        foreach (['key', 'type', 'id', 'expansionKey', 'data'] as $method) {
            if (! method_exists($entry, $method)) {
                return null;
            }
        }

        try {
            $type = strtolower(trim((string) $entry->type()));
            $key = $this->normaliseKey((string) $entry->key());
            $id = trim((string) $entry->id());
            $expansion = $this->normaliseKey((string) $entry->expansionKey());
            $data = $entry->data();
        } catch (Throwable) {
            return null;
        }

        if (
            $type !== strtolower(trim($expectedType))
            || $key === ''
            || $id === ''
            || $expansion === ''
            || ! is_array($data)
        ) {
            return null;
        }

        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            return null;
        }

        return array_merge(
            $data,
            [
                'key' => $key,
                'name' => $name,
                'canonical_id' => $id,
                'expansion' => $expansion,
                'source_kind' => 'gmrexp',
            ]
        );
    }

    private function activeCatalogue(): ?object
    {
        try {
            $catalogue = $this->resolver !== null
                ? ($this->resolver)()
                : $this->discoverActiveCatalogue();
        } catch (Throwable) {
            return null;
        }

        if (! is_object($catalogue)) {
            return null;
        }

        if (method_exists($catalogue, 'apiVersion')) {
            try {
                $version = (string) $catalogue->apiVersion();
            } catch (Throwable) {
                return null;
            }

            if (
                $version === ''
                || version_compare(
                    $version,
                    self::MINIMUM_ACTIVE_CONTENT_API_VERSION,
                    '<'
                )
            ) {
                return null;
            }
        }

        return method_exists($catalogue, 'ofType') ? $catalogue : null;
    }

    private function discoverActiveCatalogue(): mixed
    {
        $function = 'GreatMarketrealmExpansions\\active_content';
        if (! function_exists($function)) {
            return null;
        }

        return $function();
    }


    /** @return string[]|null */
    private function currentExpansionScope(): ?array
    {
        if (! function_exists('apply_filters')) {
            return null;
        }

        try {
            $scope = apply_filters(
                'gmrc_expansion_character_scope',
                null
            );
        } catch (Throwable) {
            return [];
        }

        if ($scope === null) {
            return null;
        }

        if (! is_array($scope)) {
            return [];
        }

        $keys = [];
        foreach ($scope as $key) {
            if (! is_scalar($key)) {
                continue;
            }

            $normalised = $this->normaliseKey((string) $key);
            if ($normalised !== '') {
                $keys[$normalised] = true;
            }
        }

        $keys = array_keys($keys);
        sort($keys);

        return $keys;
    }

    private function expansionLabel(string $key): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $key));
    }

    private function normaliseKey(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]+/', '-', $value);
        $value = is_string($value) ? preg_replace('/-+/', '-', $value) : '';

        return is_string($value) ? trim($value, '-') : '';
    }
}
