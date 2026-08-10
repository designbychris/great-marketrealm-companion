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

            if (in_array('finesse', $properties, true) && $dexterity > $strength) {
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
                'damage_modifier' => $abilityModifier,
                'damage_type' => $item->damageType() ?? 'damage',
                'properties' => $properties,
                'range' => 'Melee · 5 ft',
            ];
        }

        return $attacks;
    }
}
