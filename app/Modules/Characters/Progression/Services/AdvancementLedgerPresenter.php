<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Rules\ExperienceTable;

defined('ABSPATH') || exit;

final class AdvancementLedgerPresenter
{
    public function __construct(
        private ?ClassProgressionCatalogue $catalogue = null
    ) {
        $this->catalogue ??=
            new ClassProgressionCatalogue();
    }

    /** @return array<string,mixed> */
    public function present(Character $character): array
    {
        $current = $character->level();
        $eligible = $character->canAdvance();
        $target = $eligible
            ? $current->next()
            : $current;

        $classEntry = $eligible
            ? $this->catalogue->forLevel(
                $character->characterClass(),
                $target->value()
            )
            : null;

        $constitution = $character
            ->abilityScores()
            ->constitution()
            ->modifier();

        $averageHp = max(
            1,
            1 + intdiv(
                $character->characterClass()->hitDie(),
                2
            ) + $constitution
        );

        $currentProficiency =
            $character->proficiencyBonus();

        $targetProficiency = $eligible
            ? ProficiencyBonus::fromLevel($target)
            : $currentProficiency;

        $automatic = [];

        if (
            $eligible
            && ! $targetProficiency->equals(
                $currentProficiency
            )
        ) {
            $automatic[] = [
                'key' => 'proficiency-bonus',
                'label' => 'Proficiency Bonus',
                'detail' => sprintf(
                    '%s becomes %s at Level %d.',
                    $currentProficiency->signed(),
                    $targetProficiency->signed(),
                    $target->value()
                ),
            ];
        }

        return [
            'eligible' => $eligible,
            'current_level' => $current->value(),
            'target_level' => $target->value(),
            'highest_eligible_level' =>
                $character->highestEligibleLevel()->value(),
            'levels_waiting' =>
                $character->pendingAdvancementLevels(),
            'experience' =>
                $character->experience()->value(),
            'target_xp' => $eligible
                ? ExperienceTable::requiredFor($target)
                : ExperienceTable::requiredForNext($current),
            'class' =>
                $character->characterClass()->value(),
            'class_label' =>
                $character->characterClass()->label(),
            'hit_die' =>
                'd' . $character->characterClass()->hitDie(),
            'constitution_modifier' => $constitution,
            'suggested_hp_gain' => $averageHp,
            'current_proficiency' =>
                $currentProficiency->signed(),
            'target_proficiency' =>
                $targetProficiency->signed(),
            'automatic' => $automatic,
            'choices' => $eligible
                ? [
                    [
                        'key' => 'hit-points',
                        'label' => 'Hit Point Increase',
                        'status' => 'coming-next',
                        'detail' =>
                            'Choose the class average or roll the class Hit Die in The Growing Adventurer.',
                    ],
                ]
                : [],
            'class_progression' => $classEntry,
            'commit_available' => false,
        ];
    }
}
