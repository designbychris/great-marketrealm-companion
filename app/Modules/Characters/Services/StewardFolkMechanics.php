<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Services;

use GreatMarketrealmCompanion\Integration\Expansions\ExpansionCharacterCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Language;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Languages;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SkillProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiencies;

defined('ABSPATH') || exit;

/**
 * Converts a published Steward Folk record into safe Character-domain grants.
 */
final class StewardFolkMechanics
{
    public function __construct(
        private ?ExpansionCharacterCatalogue $expansions = null
    ) {
        $this->expansions ??= new ExpansionCharacterCatalogue();
    }

    /** @return array<string,mixed> */
    public function forRace(string $race): array
    {
        $key = $this->normaliseIdentifier($race);

        if (function_exists('get_option')) {
            $records = get_option('gmrc_steward_folk', []);
            $record = is_array($records) && is_array($records[$key] ?? null)
                ? $records[$key]
                : [];

            if (($record['status'] ?? '') === 'published') {
                return is_array($record['mechanics'] ?? null)
                    ? $record['mechanics']
                    : [];
            }
        }

        $expansion = $this->expansions->definition('race', $key);
        return is_array($expansion)
            ? $this->expansionMechanics($expansion)
            : [];
    }

    /** @return array<string,mixed> */
    public function resolved(string $race, ?string $heritage = null): array
    {
        $base = $this->forRace($race);
        if ($base === [] || $heritage === null || $heritage === '') {
            return $base;
        }

        if (! function_exists('get_option')) {
            return $base;
        }

        $records = get_option('gmrc_steward_folk', []);
        $record = is_array($records) && is_array($records[sanitize_key($race)] ?? null)
            ? $records[sanitize_key($race)]
            : [];

        foreach ((array) ($record['heritages'] ?? []) as $candidate) {
            if (
                ! is_array($candidate)
                || ($candidate['key'] ?? '') !== sanitize_key($heritage)
                || ($candidate['parent'] ?? '') !== sanitize_key($race)
            ) {
                continue;
            }

            $addition = is_array($candidate['mechanics'] ?? null)
                ? $candidate['mechanics']
                : [];

            return $this->mergeMechanics($base, $addition);
        }

        return $base;
    }

    public function applyAbilityModifiers(
        string $race,
        AbilityScores $scores,
        ?string $heritage = null
    ): AbilityScores {
        $modifiers = $this->resolved($race, $heritage)['ability_modifiers'] ?? [];
        $modifiers = is_array($modifiers) ? $modifiers : [];

        foreach (['strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma'] as $ability) {
            $bonus = max(0, min(4, (int) ($modifiers[$ability] ?? 0)));
            if ($bonus === 0) {
                continue;
            }

            $getter = $ability;
            $setter = 'with' . ucfirst($ability);
            $value = min(30, $scores->{$getter}()->value() + $bonus);
            $scores = $scores->{$setter}(AbilityScore::fromInt($value));
        }

        return $scores;
    }

    public function languages(string $race, ?string $heritage = null): Languages
    {
        $values = $this->resolved($race, $heritage)['automatic_languages'] ?? [];
        $values = is_array($values) ? $values : [];

        return Languages::fromStrings(array_values(array_filter(
            array_map('sanitize_key', $values),
            static fn (string $language): bool => Language::supports($language)
        )));
    }

    public function tools(string $race, ?string $heritage = null): ToolProficiencies
    {
        $values = $this->resolved($race, $heritage)['tool_proficiencies'] ?? [];
        $values = is_array($values) ? $values : [];

        return ToolProficiencies::fromStrings(array_values(array_filter(
            array_map('sanitize_key', $values),
            static fn (string $tool): bool => ToolProficiency::supports($tool)
        )));
    }

    public function skills(string $race, ?string $heritage = null): SkillProficiencies
    {
        $values = $this->resolved($race, $heritage)['skill_proficiencies'] ?? [];

        return SkillProficiencies::proficient(
            is_array($values) ? $values : []
        );
    }
    /**
     * Heritage mechanics are additive. Lists are unioned and numeric ability
     * modifiers are summed so the parent Folk remains the foundation.
     *
     * @param array<string,mixed> $base
     * @param array<string,mixed> $addition
     * @return array<string,mixed>
     */
    private function mergeMechanics(array $base, array $addition): array
    {
        $baseAbilities = is_array($base['ability_modifiers'] ?? null)
            ? $base['ability_modifiers']
            : [];
        $additionAbilities = is_array($addition['ability_modifiers'] ?? null)
            ? $addition['ability_modifiers']
            : [];

        foreach (['strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma'] as $ability) {
            $baseAbilities[$ability] = max(
                0,
                min(
                    8,
                    (int) ($baseAbilities[$ability] ?? 0)
                    + (int) ($additionAbilities[$ability] ?? 0)
                )
            );
        }

        $resolved = $base;
        $resolved['ability_modifiers'] = $baseAbilities;

        foreach (['skill_proficiencies', 'tool_proficiencies', 'automatic_languages', 'resistances'] as $key) {
            $left = is_array($base[$key] ?? null) ? $base[$key] : [];
            $right = is_array($addition[$key] ?? null) ? $addition[$key] : [];
            $resolved[$key] = array_values(array_unique(array_merge($left, $right)));
        }

        $resolved['chosen_language_count'] =
            max(0, (int) ($base['chosen_language_count'] ?? 0))
            + max(0, (int) ($addition['chosen_language_count'] ?? 0));

        return $resolved;
    }


    /** @param array<string,mixed> $definition @return array<string,mixed> */
    private function expansionMechanics(array $definition): array
    {
        $proficiencies = is_array($definition['proficiencies'] ?? null)
            ? $definition['proficiencies']
            : [];

        $skills = [];
        foreach ((array) ($proficiencies['skills'] ?? []) as $skill) {
            $skill = $this->normaliseIdentifier((string) $skill);
            if ($skill !== '') {
                $skills[] = $skill;
            }
        }

        $tools = [];
        foreach ((array) ($proficiencies['tools'] ?? []) as $tool) {
            $tool = $this->normaliseIdentifier((string) $tool);
            if ($tool !== '' && ToolProficiency::supports($tool)) {
                $tools[] = $tool;
            }
        }

        $languages = [];
        foreach ((array) ($definition['languages'] ?? []) as $language) {
            $language = $this->normaliseIdentifier((string) $language);
            if ($language !== '' && Language::supports($language)) {
                $languages[] = $language;
            }
        }

        $resistances = [];
        foreach ((array) ($definition['resistances'] ?? []) as $resistance) {
            $resistance = $this->normaliseIdentifier((string) $resistance);
            if ($resistance !== '') {
                $resistances[] = $resistance;
            }
        }

        $abilityModifiers = [];
        foreach ((array) ($definition['ability_score_rules'] ?? []) as $rule) {
            if (! is_array($rule)) {
                continue;
            }
            $ability = $this->normaliseIdentifier((string) ($rule['ability'] ?? ''));
            $amount = (int) ($rule['amount'] ?? 0);
            if (
                in_array($ability, ['strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma'], true)
                && $amount > 0
            ) {
                $abilityModifiers[$ability] = ($abilityModifiers[$ability] ?? 0) + $amount;
            }
        }

        $languageChoiceCount = 0;
        foreach ((array) ($definition['language_choices'] ?? []) as $choice) {
            if (is_array($choice)) {
                $languageChoiceCount += max(0, (int) ($choice['count'] ?? 0));
            }
        }

        return [
            'ability_modifiers' => $abilityModifiers,
            'skill_proficiencies' => array_values(array_unique($skills)),
            'tool_proficiencies' => array_values(array_unique($tools)),
            'automatic_languages' => array_values(array_unique($languages)),
            'resistances' => array_values(array_unique($resistances)),
            'chosen_language_count' => $languageChoiceCount,
        ];
    }

    private function normaliseIdentifier(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(["'", '’'], '', $value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        return is_string($value) ? trim($value, '-') : '';
    }

}
