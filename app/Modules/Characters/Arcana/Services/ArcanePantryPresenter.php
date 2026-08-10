<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Arcana\Services;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityDefinition;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Builds the Spells & Abilities Ledger pages from Character mechanics.
 */
final class ArcanePantryPresenter
{
    public function __construct(
        private ArcaneAbilityCatalogue $catalogue
    ) {
    }

    /** @return array<string, mixed> */
    public function present(Character $character): array
    {
        $class = $character->characterClass()->value();
        $castingAbility = $this->castingAbility($class);
        $castingModifier = $castingAbility === null
            ? 0
            : $character->abilityScores()
                ->all()[$castingAbility]
                ->modifier();

        $proficiency = $character->proficiencyBonus()->value();
        $spellAttack = $castingAbility === null
            ? null
            : $castingModifier + $proficiency;

        $saveDc = $castingAbility === null
            ? null
            : 8 + $castingModifier + $proficiency;

        $entries = array_map(
            fn (ArcaneAbilityDefinition $ability): array =>
                $this->entry(
                    $ability,
                    $castingModifier,
                    $spellAttack,
                    $saveDc,
                    $character->level()->value()
                ),
            $this->catalogue->forClass($class)
        );

        return [
            'casting_ability' => $castingAbility === null
                ? null
                : ucfirst($castingAbility),
            'casting_modifier' => $castingModifier,
            'spell_attack' => $spellAttack,
            'save_dc' => $saveDc,
            'slots' => $this->slots(
                $class,
                $character->level()->value()
            ),
            'entries' => $entries,
            'has_spells' => count(
                array_filter(
                    $entries,
                    static fn (array $entry): bool =>
                        in_array(
                            $entry['kind'],
                            ['spell', 'cantrip'],
                            true
                        )
                )
            ) > 0,
        ];
    }

    /** @return array<string, mixed> */
    private function entry(
        ArcaneAbilityDefinition $ability,
        int $castingModifier,
        ?int $spellAttack,
        ?int $saveDc,
        int $level
    ): array {
        $modifier = 0;

        if ($ability->addCastingModifier()) {
            $modifier = $castingModifier;
        }

        if ($ability->id() === 'second-wind') {
            $modifier = $level;
        }

        return [
            'id' => $ability->id(),
            'label' => $ability->label(),
            'kind' => $ability->kind(),
            'description' => $ability->description(),
            'activation' => $ability->activation(),
            'range' => $ability->range(),
            'duration' => $ability->duration(),
            'uses' => $ability->uses(),
            'roll_kind' => $ability->rollKind(),
            'formula' => $ability->formula(),
            'damage_type' => $ability->damageType(),
            'save_ability' => $ability->saveAbility(),
            'save_dc' => $ability->saveAbility() !== null
                ? $saveDc
                : null,
            'spell_attack' => $ability->isSpellAttack()
                ? $spellAttack
                : null,
            'roll_modifier' => $modifier,
        ];
    }

    private function castingAbility(string $class): ?string
    {
        return match ($class) {
            'artificer', 'wizard' => 'intelligence',
            'cleric', 'druid', 'ranger' => 'wisdom',
            'bard', 'paladin', 'sorcerer', 'warlock' => 'charisma',
            default => null,
        };
    }

    /** @return array<int, array{level:int,total:int}> */
    private function slots(string $class, int $level): array
    {
        if ($level < 1) {
            return [];
        }

        if ($class === 'warlock') {
            return [['level' => 1, 'total' => 1]];
        }

        if (
            in_array(
                $class,
                ['artificer', 'bard', 'cleric', 'druid', 'sorcerer', 'wizard'],
                true
            )
        ) {
            return [['level' => 1, 'total' => 2]];
        }

        return [];
    }
}
