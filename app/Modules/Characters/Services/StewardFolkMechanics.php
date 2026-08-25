<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Services;

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
    /** @return array<string,mixed> */
    public function forRace(string $race): array
    {
        if (! function_exists('get_option')) {
            return [];
        }

        $records = get_option('gmrc_steward_folk', []);
        $key = sanitize_key($race);
        $record = is_array($records) && is_array($records[$key] ?? null)
            ? $records[$key]
            : [];

        if (($record['status'] ?? '') !== 'published') {
            return [];
        }

        return is_array($record['mechanics'] ?? null)
            ? $record['mechanics']
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

}
