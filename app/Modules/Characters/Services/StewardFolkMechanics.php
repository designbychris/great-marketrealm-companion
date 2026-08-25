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

    public function applyAbilityModifiers(
        string $race,
        AbilityScores $scores
    ): AbilityScores {
        $modifiers = $this->forRace($race)['ability_modifiers'] ?? [];
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

    public function languages(string $race): Languages
    {
        $values = $this->forRace($race)['automatic_languages'] ?? [];
        $values = is_array($values) ? $values : [];

        return Languages::fromStrings(array_values(array_filter(
            array_map('sanitize_key', $values),
            static fn (string $language): bool => Language::supports($language)
        )));
    }

    public function tools(string $race): ToolProficiencies
    {
        $values = $this->forRace($race)['tool_proficiencies'] ?? [];
        $values = is_array($values) ? $values : [];

        return ToolProficiencies::fromStrings(array_values(array_filter(
            array_map('sanitize_key', $values),
            static fn (string $tool): bool => ToolProficiency::supports($tool)
        )));
    }

    public function skills(string $race): SkillProficiencies
    {
        $values = $this->forRace($race)['skill_proficiencies'] ?? [];

        return SkillProficiencies::proficient(
            is_array($values) ? $values : []
        );
    }
}
