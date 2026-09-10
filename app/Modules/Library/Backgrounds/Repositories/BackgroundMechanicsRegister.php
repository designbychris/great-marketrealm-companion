<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories;

use GreatMarketrealmCompanion\Integration\Expansions\ExpansionCharacterCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Language;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SkillProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;
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

    public function __construct(
        private ?HandbookBackgroundRegister $handbook = null,
        private ?ExpansionCharacterCatalogue $expansions = null
    ) {
        $this->handbook ??= new HandbookBackgroundRegister();
        $this->expansions ??= new ExpansionCharacterCatalogue();
    }

    /** @return BackgroundRecord[] */
    public function all(): array
    {
        $canonical = array_map(
            fn (BackgroundRecord $record): BackgroundRecord => $this->resolved($record),
            $this->handbook->all()
        );

        return array_merge(
            $canonical,
            array_values($this->stewardRecords()),
            array_values($this->expansionRecords())
        );
    }

    public function find(string $key): ?BackgroundRecord
    {
        $key = sanitize_key($key);
        $record = $this->handbook->find($key);
        if ($record instanceof BackgroundRecord) {
            return $this->resolved($record);
        }

        return $this->stewardRecords()[$key]
            ?? $this->expansionRecords()[$key]
            ?? null;
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
            $record->name(),
            $record->languageChoices(),
            $record->fixedLanguages()
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


    /** @return array<string,BackgroundRecord> */
    private function expansionRecords(): array
    {
        $records = [];

        foreach ($this->expansions->backgrounds() as $key => $entry) {
            $proficiencies = is_array($entry['proficiencies'] ?? null)
                ? $entry['proficiencies']
                : [];

            $skills = $this->normaliseSkills(
                is_array($proficiencies['skills'] ?? null)
                    ? $proficiencies['skills']
                    : []
            );
            $tools = $this->normaliseTools(
                is_array($proficiencies['tools'] ?? null)
                    ? $proficiencies['tools']
                    : []
            );

            $features = is_array($entry['features'] ?? null)
                ? array_values($entry['features'])
                : [];
            $feature = is_array($features[0] ?? null) ? $features[0] : [];
            $featureName = trim((string) ($feature['name'] ?? ''));
            $featureDetail = trim((string) ($feature['description'] ?? ''));

            if ($featureName === '' || $featureDetail === '') {
                continue;
            }

            $sourceIssues = [];
            if (count($features) > 1) {
                $sourceIssues[] = 'The Companion background card currently projects the first Almanac background feature only.';
            }

            $fixedLanguages = $this->normaliseLanguages(
                is_array($entry['languages'] ?? null)
                    ? $entry['languages']
                    : []
            );

            $languageChoices = 0;
            if (is_array($entry['language_choices'] ?? null)) {
                foreach ($entry['language_choices'] as $choice) {
                    if (! is_array($choice)) {
                        continue;
                    }
                    $languageChoices += max(
                        0,
                        (int) ($choice['count'] ?? 0)
                    );
                }
            }

            $record = new BackgroundRecord(
                $key,
                trim((string) ($entry['name'] ?? '')),
                $featureName,
                $featureDetail,
                $skills,
                $tools,
                $this->toolLabel($tools, ''),
                $sourceIssues,
                $languageChoices,
                $fixedLanguages,
                is_string($entry['canonical_id'] ?? null)
                    ? $entry['canonical_id']
                    : null,
                is_string($entry['expansion'] ?? null)
                    ? $entry['expansion']
                    : null
            );

            if ($record->key() !== '' && $record->name() !== '') {
                $records[$record->key()] = $record;
            }
        }

        return $records;
    }

    /** @param array<int,mixed> $values @return array<int,string> */
    private function normaliseSkills(array $values): array
    {
        $skills = [];
        foreach ($values as $value) {
            $skill = $this->normaliseIdentifier((string) $value);
            if ($skill !== '' && SkillProficiencies::supports($skill)) {
                $skills[] = $skill;
            }
        }

        return SkillProficiencies::proficient($skills)->proficiencies();
    }

    /** @param array<int,mixed> $values @return array<int,string> */
    private function normaliseTools(array $values): array
    {
        $tools = [];
        foreach ($values as $value) {
            $tool = $this->normaliseIdentifier((string) $value);
            if ($tool !== '' && ToolProficiency::supports($tool)) {
                $tools[] = $tool;
            }
        }
        return array_values(array_unique($tools));
    }

    /** @param array<int,mixed> $values @return array<int,string> */
    private function normaliseLanguages(array $values): array
    {
        $languages = [];
        foreach ($values as $value) {
            $language = $this->normaliseIdentifier((string) $value);
            if ($language !== '' && Language::supports($language)) {
                $languages[] = $language;
            }
        }
        return array_values(array_unique($languages));
    }

    private function normaliseIdentifier(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(["'", '’'], '', $value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        return is_string($value) ? trim($value, '-') : '';
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
