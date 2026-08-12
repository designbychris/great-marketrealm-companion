<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Rules\ExperienceTable;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\RisingFolioBuilder;

defined('ABSPATH') || exit;

final class AdvancementLedgerPresenter
{
    public function __construct(
        private ?ClassProgressionCatalogue $catalogue = null,
        private ?RisingFolioBuilder $folioBuilder = null
    ) {
        $this->catalogue ??=
            new ClassProgressionCatalogue();

        $this->folioBuilder ??=
            new RisingFolioBuilder();
    }

    /** @return array<string,mixed> */
    /**
     * @param array<string,array<int,string>> $choices
     */
    public function present(
        Character $character,
        array $choices = []
    ): array {
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

        $folios = $eligible
            ? $this->folioBuilder->forAdvancement(
                $character,
                $target->value(),
                $choices
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
            'folios' => $folios
                ? $folios->toArray()
                : [],
            'folio_total' => $folios
                ? $folios->total()
                : 0,
            'folio_ready_count' => $folios
                ? $folios->readyCount()
                : 0,
            'folio_attention_count' => $folios
                ? $folios->attentionCount()
                : 0,
            'folios_complete' => $folios
                ? $folios->allReady()
                : false,
            'recorded_choices' => $choices,
            'commit_available' => false,
        ];
    }
}
