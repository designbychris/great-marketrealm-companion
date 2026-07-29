<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Characters;

use GreatMarketrealmCompanion\Services\Registry\Registry;
use GreatMarketrealmCompanion\Services\Registry\RegistryItem;

final class ClassRegistry extends Registry
{
    protected function register(): void
    {
        $this->addClass(
            key: 'artificer',
            name: 'Artificer',
            hitDie: 'd8',
            primaryAbility: 'Intelligence',
            spellcaster: true,
            guildSeal: 'artificer',
        );

        $this->addClass(
            key: 'barbarian',
            name: 'Barbarian',
            hitDie: 'd12',
            primaryAbility: 'Strength',
            spellcaster: false,
            guildSeal: 'barbarian',
        );

        $this->addClass(
            key: 'bard',
            name: 'Bard',
            hitDie: 'd8',
            primaryAbility: 'Charisma',
            spellcaster: true,
            guildSeal: 'bard',
        );

        $this->addClass(
            key: 'cleric',
            name: 'Cleric',
            hitDie: 'd8',
            primaryAbility: 'Wisdom',
            spellcaster: true,
            guildSeal: 'cleric',
        );

        $this->addClass(
            key: 'druid',
            name: 'Druid',
            hitDie: 'd8',
            primaryAbility: 'Wisdom',
            spellcaster: true,
            guildSeal: 'druid',
        );

        $this->addClass(
            key: 'fighter',
            name: 'Fighter',
            hitDie: 'd10',
            primaryAbility: 'Strength or Dexterity',
            spellcaster: false,
            guildSeal: 'fighter',
        );

        $this->addClass(
            key: 'monk',
            name: 'Monk',
            hitDie: 'd8',
            primaryAbility: 'Dexterity and Wisdom',
            spellcaster: false,
            guildSeal: 'monk',
        );

        $this->addClass(
            key: 'paladin',
            name: 'Paladin',
            hitDie: 'd10',
            primaryAbility: 'Strength and Charisma',
            spellcaster: true,
            guildSeal: 'paladin',
        );

        $this->addClass(
            key: 'ranger',
            name: 'Ranger',
            hitDie: 'd10',
            primaryAbility: 'Dexterity and Wisdom',
            spellcaster: true,
            guildSeal: 'ranger',
        );

        $this->addClass(
            key: 'rogue',
            name: 'Rogue',
            hitDie: 'd8',
            primaryAbility: 'Dexterity',
            spellcaster: false,
            guildSeal: 'rogue',
        );

        $this->addClass(
            key: 'sorcerer',
            name: 'Sorcerer',
            hitDie: 'd6',
            primaryAbility: 'Charisma',
            spellcaster: true,
            guildSeal: 'sorcerer',
        );

        $this->addClass(
            key: 'warlock',
            name: 'Warlock',
            hitDie: 'd8',
            primaryAbility: 'Charisma',
            spellcaster: true,
            guildSeal: 'warlock',
        );

        $this->addClass(
            key: 'wizard',
            name: 'Wizard',
            hitDie: 'd6',
            primaryAbility: 'Intelligence',
            spellcaster: true,
            guildSeal: 'wizard',
        );
    }

    private function addClass(
        string $key,
        string $name,
        string $hitDie,
        string $primaryAbility,
        bool $spellcaster,
        string $guildSeal
    ): void {
        $this->add(
            new RegistryItem(
                key: $key,
                name: $name,
                attributes: [
                    'hit_die'         => $hitDie,
                    'primary_ability' => $primaryAbility,
                    'spellcaster'     => $spellcaster,
                    'guild_seal'      => $guildSeal,
                ],
            )
        );
    }
}
