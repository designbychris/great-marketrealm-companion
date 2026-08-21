<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Combat\Services;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\CharacterInventory;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/** Builds attack cards from the weapons an adventurer currently has equipped. */
final class AttackPresenter
{
    public function __construct(private ItemCatalogue $catalogue) {}

    /** @return array<int, array<string, mixed>> */
    public function present(Character $character, CharacterInventory $inventory): array
    {
        $attacks = [];
        $strength = $character->abilityScores()->strength()->modifier();
        $dexterity = $character->abilityScores()->dexterity()->modifier();
        $proficiency = $character->proficiencyBonus()->value();

        foreach ($inventory->equipped() as $entry) {
            $item = $this->catalogue->find($entry->itemId());
            if ($item === null || ! $item->isWeapon() || $item->damageDie() === null) {
                continue;
            }

            $properties = $item->properties();
            $ability = 'Strength';
            $abilityModifier = $strength;

            if (in_array('ranged', $properties, true)) {
                $ability = 'Dexterity';
                $abilityModifier = $dexterity;
            } elseif (
                in_array('finesse', $properties, true)
                && $dexterity > $strength
            ) {
                $ability = 'Dexterity';
                $abilityModifier = $dexterity;
            }

            $attacks[] = [
                'id' => $item->id(),
                'label' => $item->label(),
                'description' => $item->description(),
                'ability' => $ability,
                'ability_modifier' => $abilityModifier,
                'proficiency_bonus' => $proficiency,
                'attack_bonus' => $abilityModifier + $proficiency,
                'damage_die' => $item->damageDie(),
                'critical_damage_die' => $this->criticalDamageFormula(
                    $item->damageDie()
                ),
                'damage_modifier' => $abilityModifier,
                'damage_type' => $item->damageType() ?? 'damage',
                'target_mode' => 'creature',
                'default_target_kind' => '',
                'properties' => $properties,
                'range' => $item->range()
                    ?? 'Melee · 5 ft',
            ];
        }

        return $attacks;
    }

    /**
     * Double only the weapon dice for a critical hit.
     *
     * Flat ability modifiers are deliberately not part of this formula and
     * therefore remain single when Diceworks performs the follow-up roll.
     */
    private function criticalDamageFormula(string $formula): string
    {
        if (! preg_match(
            '/^(\d+)d(4|6|8|10|12|20|100)$/i',
            trim($formula),
            $matches
        )) {
            return $formula;
        }

        return ((int) $matches[1] * 2)
            . 'd'
            . (int) $matches[2];
    }
}
