<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Characters;

use GreatMarketrealmCompanion\Services\Definitions\Definitions;
use GreatMarketrealmCompanion\Services\Registry\Registry;

defined('ABSPATH') || exit;

/**
 * Registry of playable Marketrealm classes.
 *
 * @since 0.3.0
 */
final class ClassRegistry extends Registry
{
    /**
     * Create the Class Registry.
     */
    public function __construct(
        private Definitions $definitions
    ) {
        parent::__construct();
    }

    /**
     * Register the playable classes.
     */
    protected function register(): void
    {
        $scriptorium = $this->definitions->scriptorium();

        $scriptorium
            ->characterClass(
                key: 'grocer',
                name: 'Grocer'
            )
                ->description(
                    'Masters of logistics, stock control and improvised produce-based combat.'
                )
                ->hitDie(8)
                ->primaryAbility('Wisdom')
                ->savingThrow('Wisdom')
                ->savingThrow('Charisma')
                ->armourProficiency('Light Armour')
                ->weaponProficiency('Simple Weapons')
                ->toolProficiency('Merchant Tools')
                ->startingEquipment('A merchant ledger')
                ->startingEquipment('A simple weapon')
                ->feature('Fresh Produce')
                ->feature('Bulk Discount')
                ->source('Great Marketrealm Core Rules')
                ->tag('core')
                ->done()

            ->characterClass(
                key: 'cleaver-saint',
                name: 'Cleaver Saint'
            )
                ->description(
                    'Sacred warriors who defend the Marketrealm with sharpened steel and unwavering conviction.'
                )
                ->hitDie(10)
                ->primaryAbility('Strength')
                ->primaryAbility('Charisma')
                ->savingThrow('Wisdom')
                ->savingThrow('Charisma')
                ->spellcastingAbility('Charisma')
                ->armourProficiency('All Armour')
                ->armourProficiency('Shields')
                ->weaponProficiency('Simple Weapons')
                ->weaponProficiency('Martial Weapons')
                ->multiclassRequirement('Strength 13')
                ->multiclassRequirement('Charisma 13')
                ->feature('Sacred Carving')
                ->feature('Marketrealm Smite')
                ->source('Great Marketrealm Core Rules')
                ->tag('core')
                ->done();

        $this->registerDefinitions(
            $scriptorium->definitions()
        );
    }
}
