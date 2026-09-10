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

    private function normaliseKey(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]+/', '-', $value);
        $value = is_string($value) ? preg_replace('/-+/', '-', $value) : '';

        return is_string($value) ? trim($value, '-') : '';
    }
}
