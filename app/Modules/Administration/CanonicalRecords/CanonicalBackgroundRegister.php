<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords;

use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Models\BackgroundRecord;
use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories\HandbookBackgroundRegister;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Steward-facing Players Handbook background register.
 *
 * The bundled Handbook transcription stays immutable. Steward wording lives in
 * an option overlay, while skills/tools remain certified mechanical identity
 * until the dedicated background-mechanics bridge is introduced.
 */
final class CanonicalBackgroundRegister
{
    public const OPTION = 'gmrc_canonical_background_overrides';
    private const SOURCE = 'The Great Marketrealm - Players Handbook';

    public function __construct(private ?HandbookBackgroundRegister $handbook = null)
    {
        $this->handbook ??= new HandbookBackgroundRegister();
    }

    /** @return CanonicalBackground[] */
    public function all(): array
    {
        return array_map(fn (BackgroundRecord $record): CanonicalBackground => $this->map($record), $this->handbook->all());
    }

    public function find(string $key): ?CanonicalBackground
    {
        $record = $this->handbook->find(sanitize_key($key));
        return $record instanceof BackgroundRecord ? $this->map($record) : null;
    }

    /** @param array<string,mixed> $input */
    public function save(string $key, array $input): void
    {
        $record = $this->find($key);
        if (! $record) {
            throw new RuntimeException('Canonical Background record not found.');
        }

        $overrides = $this->overrides();
        $overrides[$record->key()] = [
            'name' => sanitize_text_field((string) ($input['name'] ?? '')),
            'feature_name' => sanitize_text_field((string) ($input['feature_name'] ?? '')),
            'feature_detail' => sanitize_textarea_field((string) ($input['feature_detail'] ?? '')),
            'steward_notes' => sanitize_textarea_field((string) ($input['steward_notes'] ?? '')),
        ];
        update_option(self::OPTION, $overrides, false);
    }

    public function reset(string $key): void
    {
        $record = $this->find($key);
        if (! $record) {
            throw new RuntimeException('Canonical Background record not found.');
        }

        $overrides = $this->overrides();
        unset($overrides[$record->key()]);
        update_option(self::OPTION, $overrides, false);
    }

    public function hasOverride(CanonicalBackground $record): bool
    {
        return isset($this->overrides()[$record->key()]);
    }

    private function map(BackgroundRecord $record): CanonicalBackground
    {
        $override = $this->overrides()[$record->key()] ?? [];
        return new CanonicalBackground(
            $record->key(),
            trim((string) ($override['name'] ?? $record->name())),
            trim((string) ($override['feature_name'] ?? $record->featureName())),
            trim((string) ($override['feature_detail'] ?? $record->featureDetail())),
            $record->skills(),
            $record->tools(),
            $record->toolLabel(),
            $record->sourceIssues(),
            self::SOURCE,
            trim((string) ($override['steward_notes'] ?? ''))
        );
    }

    /** @return array<string,array<string,string>> */
    private function overrides(): array
    {
        $value = get_option(self::OPTION, []);
        return is_array($value) ? $value : [];
    }
}
