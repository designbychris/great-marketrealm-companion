<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Characters;

use GreatMarketrealmCompanion\Services\Registry\Registry;

final class ClassRegistry extends Registry
{
    protected function register(): void
    {
        $this->registerItem(
            key: 'artificer',
            name: 'Artificer',
            attributes: [
                'hit_die'         => 'd8',
                'primary_ability' => 'Intelligence',
                'spellcaster'     => true,
                'guild_seal'      => 'artificer',
            ],
        );

        $this->registerItem(
            key: 'barbarian',
            name: 'Barbarian',
            attributes: [
                'hit_die'         => 'd12',
                'primary_ability' => 'Strength',
                'spellcaster'     => false,
                'guild_seal'      => 'barbarian',
            ],
        );

        $this->registerItem(
            key: 'bard',
            name: 'Bard',
            attributes: [
                'hit_die'         => 'd8',
                'primary_ability' => 'Charisma',
                'spellcaster'     => true,
                'guild_seal'      => 'bard',
            ],
        );

        $this->registerItem(
            key: 'cleric',
            name: 'Cleric',
            attributes: [
                'hit_die'         => 'd8',
                'primary_ability' => 'Wisdom',
                'spellcaster'     => true,
                'guild_seal'      => 'cleric',
            ],
        );

        $this->registerItem(
            key: 'druid',
            name: 'Druid',
            attributes: [
                'hit_die'         => 'd8',
                'primary_ability' => 'Wisdom',
                'spellcaster'     => true,
                'guild_seal'      => 'druid',
            ],
        );

        $this->registerItem(
            key: 'fighter',
            name: 'Fighter',
            attributes: [
                'hit_die'         => 'd10',
                'primary_ability' => 'Strength or Dexterity',
                'spellcaster'     => false,
                'guild_seal'      => 'fighter',
            ],
        );

        $this->registerItem(
            key: 'monk',
            name: 'Monk',
            attributes: [
                'hit_die'         => 'd8',
                'primary_ability' => 'Dexterity and Wisdom',
                'spellcaster'     => false,
                'guild_seal'      => 'monk',
            ],
        );

        $this->registerItem(
            key: 'paladin',
            name: 'Paladin',
            attributes: [
                'hit_die'         => 'd10',
                'primary_ability' => 'Strength and Charisma',
                'spellcaster'     => true,
                'guild_seal'      => 'paladin',
            ],
        );

        $this->registerItem(
            key: 'ranger',
            name: 'Ranger',
            attributes: [
                'hit_die'         => 'd10',
                'primary_ability' => 'Dexterity and Wisdom',
                'spellcaster'     => true,
                'guild_seal'      => 'ranger',
            ],
        );

        $this->registerItem(
            key: 'rogue',
            name: 'Rogue',
            attributes: [
                'hit_die'         => 'd8',
                'primary_ability' => 'Dexterity',
                'spellcaster'     => false,
                'guild_seal'      => 'rogue',
            ],
        );

        $this->registerItem(
            key: 'sorcerer',
            name: 'Sorcerer',
            attributes: [
                'hit_die'         => 'd6',
                'primary_ability' => 'Charisma',
                'spellcaster'     => true,
                'guild_seal'      => 'sorcerer',
            ],
        );

        $this->registerItem(
            key: 'warlock',
            name: 'Warlock',
            attributes: [
                'hit_die'         => 'd8',
                'primary_ability' => 'Charisma',
                'spellcaster'     => true,
                'guild_seal'      => 'warlock',
            ],
        );

        $this->registerItem(
            key: 'wizard',
            name: 'Wizard',
            attributes: [
                'hit_die'         => 'd6',
                'primary_ability' => 'Intelligence',
                'spellcaster'     => true,
                'guild_seal'      => 'wizard',
            ],
        );
    }
}
