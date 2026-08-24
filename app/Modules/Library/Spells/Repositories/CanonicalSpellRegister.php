<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Spells\Repositories;

use GreatMarketrealmCompanion\Modules\Library\Spells\Models\SpellRecord;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Players Handbook spell register with Steward-maintained presentation overlay.
 *
 * Mechanical identity (kind, original spell, level, school, access and variant
 * identity) remains sourced from the bundled Handbook transcription. Steward
 * overrides may tune the Marketrealm display name and canonical rules wording.
 */
final class CanonicalSpellRegister
{
    public const OPTION = 'gmrc_canonical_spell_overrides';

    /** @var array<string,SpellRecord>|null */
    private ?array $records = null;

    public function __construct(private ?HandbookSpellRegister $handbook = null)
    {
        $this->handbook ??= new HandbookSpellRegister();
    }

    /** @return array<int,SpellRecord> */
    public function all(): array
    {
        return array_values($this->records());
    }

    public function find(string $key): ?SpellRecord
    {
        return $this->records()[sanitize_key($key)] ?? null;
    }

    /** @return array<int,SpellRecord> */
    public function byKind(string $kind): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (SpellRecord $spell): bool => $spell->kind() === $kind
        ));
    }

    /** @return array<int,SpellRecord> */
    public function byLevel(?int $level): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (SpellRecord $spell): bool => $spell->level() === $level
        ));
    }

    /** @return array<int,SpellRecord> */
    public function withSourceIssues(): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (SpellRecord $spell): bool => $spell->sourceIssues() !== []
        ));
    }

    public function sourceVariantCount(): int
    {
        return array_sum(array_map(
            static fn (SpellRecord $spell): int => count($spell->variants()),
            $this->all()
        ));
    }

    /** @param array<string,mixed> $input */
    public function save(string $key, array $input): void
    {
        $spell = $this->handbook->find(sanitize_key($key));
        if (! $spell) {
            throw new RuntimeException('Canonical Spell record not found.');
        }

        $variantTexts = is_array($input['variant_texts'] ?? null)
            ? $input['variant_texts']
            : [];
        $cleanTexts = [];
        foreach ($spell->variants() as $variant) {
            $variantId = (string) ($variant['source_variant'] ?? '');
            if ($variantId === '') {
                continue;
            }
            $cleanTexts[$variantId] = sanitize_textarea_field(
                (string) ($variantTexts[$variantId] ?? $variant['source_text'] ?? '')
            );
        }

        $overrides = $this->overrides();
        $overrides[$spell->key()] = [
            'name' => sanitize_text_field((string) ($input['name'] ?? $spell->name())),
            'variant_texts' => $cleanTexts,
            'steward_notes' => sanitize_textarea_field((string) ($input['steward_notes'] ?? '')),
        ];
        update_option(self::OPTION, $overrides, false);
        $this->flush();
    }

    public function reset(string $key): void
    {
        $spell = $this->handbook->find(sanitize_key($key));
        if (! $spell) {
            throw new RuntimeException('Canonical Spell record not found.');
        }

        $overrides = $this->overrides();
        unset($overrides[$spell->key()]);
        update_option(self::OPTION, $overrides, false);
        $this->flush();
    }

    public function hasOverride(SpellRecord $spell): bool
    {
        return isset($this->overrides()[$spell->key()]);
    }

    public function stewardNotes(SpellRecord $spell): string
    {
        return trim((string) ($this->overrides()[$spell->key()]['steward_notes'] ?? ''));
    }

    public function flush(): void
    {
        $this->records = null;
    }

    /** @return array<string,SpellRecord> */
    private function records(): array
    {
        if ($this->records !== null) {
            return $this->records;
        }

        $records = [];
        $overrides = $this->overrides();
        foreach ($this->handbook->all() as $spell) {
            $data = $spell->toArray();
            $override = is_array($overrides[$spell->key()] ?? null)
                ? $overrides[$spell->key()]
                : [];

            $data['name'] = trim((string) ($override['name'] ?? $spell->name()));
            $texts = is_array($override['variant_texts'] ?? null)
                ? $override['variant_texts']
                : [];
            $data['variants'] = array_map(
                static function (array $variant) use ($texts): array {
                    $variantId = (string) ($variant['source_variant'] ?? '');
                    if ($variantId !== '' && array_key_exists($variantId, $texts)) {
                        $variant['source_text'] = (string) $texts[$variantId];
                    }
                    return $variant;
                },
                $spell->variants()
            );

            $records[$spell->key()] = SpellRecord::fromArray($data);
        }

        return $this->records = $records;
    }

    /** @return array<string,array<string,mixed>> */
    private function overrides(): array
    {
        $value = \function_exists('get_option')
            ? \get_option(self::OPTION, [])
            : [];
        return is_array($value) ? $value : [];
    }
}
