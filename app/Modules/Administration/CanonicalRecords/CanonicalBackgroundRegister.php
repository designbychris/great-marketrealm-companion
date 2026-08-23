<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords;

use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Models\BackgroundRecord;
use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories\HandbookBackgroundRegister;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SkillProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;
use InvalidArgumentException;
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

        $skills = $this->skills($input['skills'] ?? []);
        $tools = $this->tools($input['tools'] ?? []);

        $overrides = $this->overrides();
        $overrides[$record->key()] = [
            'name' => sanitize_text_field((string) ($input['name'] ?? '')),
            'feature_name' => sanitize_text_field((string) ($input['feature_name'] ?? '')),
            'feature_detail' => sanitize_textarea_field((string) ($input['feature_detail'] ?? '')),
            'skills' => $skills,
            'tools' => $tools,
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
            is_array($override['skills'] ?? null) ? array_values($override['skills']) : $record->skills(),
            is_array($override['tools'] ?? null) ? array_values($override['tools']) : $record->tools(),
            $this->toolLabel(is_array($override['tools'] ?? null) ? array_values($override['tools']) : $record->tools(), $record->toolLabel()),
            $record->sourceIssues(),
            self::SOURCE,
            trim((string) ($override['steward_notes'] ?? ''))
        );
    }

    /** @return array<int,string> */
    private function skills(mixed $value): array
    {
        $skills = is_array($value) ? array_values(array_unique(array_map('sanitize_key', $value))) : [];
        if (count($skills) !== 2) {
            throw new RuntimeException('Choose exactly two certified Background skill proficiencies.');
        }

        try {
            return SkillProficiencies::proficient($skills)->proficiencies();
        } catch (InvalidArgumentException) {
            throw new RuntimeException('Choose recognised Character skill proficiencies.');
        }
    }

    /** @return array<int,string> */
    private function tools(mixed $value): array
    {
        $tools = is_array($value) ? array_values(array_unique(array_map('sanitize_key', $value))) : [];
        if (count($tools) !== 1 || ! ToolProficiency::supports($tools[0])) {
            throw new RuntimeException('Choose one recognised Background tool proficiency.');
        }

        return $tools;
    }

    private function toolLabel(array $tools, string $fallback): string
    {
        return count($tools) === 1 && ToolProficiency::supports((string) $tools[0])
            ? ToolProficiency::fromString((string) $tools[0])->label()
            : $fallback;
    }

    /** @return array<string,array<string,mixed>> */
    private function overrides(): array
    {
        $value = get_option(self::OPTION, []);
        return is_array($value) ? $value : [];
    }
}
