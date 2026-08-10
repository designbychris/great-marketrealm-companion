<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use GreatMarketrealmCompanion\Modules\Characters\Rules\ExperienceTable;

defined('ABSPATH') || exit;

final class RisingRegisterPresenter
{
    /** @return array<string,mixed> */
    public function present(Character $character): array
    {
        $level = $character->level();
        $experience = $character->experience()->value();
        $levelStart = ExperienceTable::requiredFor($level);
        $nextRequired = ExperienceTable::requiredForNext($level);
        $span = $nextRequired === null ? 0 : max(1, $nextRequired - $levelStart);
        $earned = max(0, $experience - $levelStart);
        $progress = $nextRequired === null
            ? 100
            : min(100, (int) floor(($earned / $span) * 100));
        $constitution = $character->abilityScores()->constitution()->modifier();
        $hpGain = max(
            1,
            1 + intdiv($character->characterClass()->hitDie(), 2) + $constitution
        );

        return [
            'level' => $level->value(),
            'experience' => $experience,
            'level_start_xp' => $levelStart,
            'next_level_xp' => $nextRequired,
            'xp_to_next' => $nextRequired === null ? 0 : max(0, $nextRequired - $experience),
            'progress_percent' => $progress,
            'can_level_up' => $character->canLevelUp(),
            'is_maximum' => $nextRequired === null,
            'next_level' => $nextRequired === null ? null : $level->value() + 1,
            'current_proficiency' => $character->proficiencyBonus()->signed(),
            'next_proficiency' => $nextRequired === null
                ? null
                : ProficiencyBonus::fromLevel($level->next())->signed(),
            'hit_die' => 'd' . $character->characterClass()->hitDie(),
            'next_hit_point_gain' => $hpGain,
            'current_max_hp' => $character->hitPoints()->maximum(),
        ];
    }
}
