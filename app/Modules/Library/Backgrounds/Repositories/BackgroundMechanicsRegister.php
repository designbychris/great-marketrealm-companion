<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Models\BackgroundRecord;

defined('ABSPATH') || exit;

/**
 * Resolve the current canonical mechanics for optional Handbook backgrounds.
 *
 * The Players Handbook stays immutable. Steward overrides are applied only
 * when preparing future-character choices and Guild Library reference data.
 */
final class BackgroundMechanicsRegister
{
    public const OPTION = 'gmrc_canonical_background_overrides';
    public const STEWARD_OPTION = 'gmrc_steward_backgrounds';

    public function __construct(private ?HandbookBackgroundRegister $handbook = null)
    {
        $this->handbook ??= new HandbookBackgroundRegister();
    }

    /** @return BackgroundRecord[] */
    public function all(): array
    {
        $canonical = array_map(
            fn (BackgroundRecord $record): BackgroundRecord => $this->resolved($record),
            $this->handbook->all()
        );

        return array_merge($canonical, array_values($this->stewardRecords()));
    }

    public function find(string $key): ?BackgroundRecord
    {
        $key = sanitize_key($key);
        $record = $this->handbook->find($key);
        if ($record instanceof BackgroundRecord) {
            return $this->resolved($record);
        }

        return $this->stewardRecords()[$key] ?? null;
    }

    public function background(string $key): Background
    {
        $record = $this->find($key);
        if (! $record instanceof BackgroundRecord) {
            return Background::fromString($key);
        }

        return Background::fromStringWithMechanics(
            $record->key(),
            $record->skills(),
            $record->tools(),
            $record->name()
        );
    }

    private function resolved(BackgroundRecord $record): BackgroundRecord
    {
        $override = $this->overrides()[$record->key()] ?? [];
        $skills = is_array($override['skills'] ?? null)
            ? array_values($override['skills'])
            : $record->skills();
        $tools = is_array($override['tools'] ?? null)
            ? array_values($override['tools'])
            : $record->tools();

        return new BackgroundRecord(
            $record->key(),
            trim((string) ($override['name'] ?? $record->name())),
            trim((string) ($override['feature_name'] ?? $record->featureName())),
            trim((string) ($override['feature_detail'] ?? $record->featureDetail())),
            $skills,
            $tools,
            $this->toolLabel($tools, $record->toolLabel()),
            $record->sourceIssues()
        );
    }

    private function toolLabel(array $tools, string $fallback): string
    {
        if (count($tools) !== 1) {
            return $fallback;
        }

        $tool = (string) $tools[0];
        if (! \GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency::supports($tool)) {
            return $fallback;
        }

        return \GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency::fromString($tool)->label();
    }


    /** @return array<string,BackgroundRecord> */
    private function stewardRecords(): array
    {
        $stored = \function_exists('get_option') ? \get_option(self::STEWARD_OPTION, []) : [];
        if (! is_array($stored)) {
            return [];
        }

        $records = [];
        foreach ($stored as $key => $entry) {
            if (! is_array($entry) || ($entry['status'] ?? '') !== 'published') {
                continue;
            }
            $skills = is_array($entry['skills'] ?? null) ? array_values($entry['skills']) : [];
            $tools = is_array($entry['tools'] ?? null) ? array_values($entry['tools']) : [];
            if (count($skills) !== 2 || count($tools) !== 1) {
                continue;
            }
            $record = new BackgroundRecord(
                sanitize_key((string) ($entry['key'] ?? $key)),
                trim((string) ($entry['name'] ?? '')),
                trim((string) ($entry['feature_name'] ?? '')),
                trim((string) ($entry['feature_detail'] ?? '')),
                $skills,
                $tools,
                trim((string) ($entry['tool_label'] ?? '')),
                []
            );
            if ($record->key() !== '' && $record->name() !== '' && $record->featureName() !== '' && $record->featureDetail() !== '') {
                $records[$record->key()] = $record;
            }
        }
        return $records;
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
