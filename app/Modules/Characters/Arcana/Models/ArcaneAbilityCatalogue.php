<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Arcana\Models;

defined('ABSPATH') || exit;

/**
 * Phase III.5 spell and feature catalogue.
 */
final class ArcaneAbilityCatalogue
{
    /** @var ArcaneAbilityDefinition[] */
    private array $abilities;

    public function __construct()
    {
        $this->abilities = [
            new ArcaneAbilityDefinition(
                'produce-spark',
                'Produce Spark',
                'cantrip',
                ['wizard', 'sorcerer', 'artificer'],
                'A bright seed of market-light streaks toward one creature.',
                '1 action',
                '60 ft',
                'Instantaneous',
                'At will',
                'damage',
                '1d10',
                'radiant',
                null,
                false,
                true
            ),
            new ArcaneAbilityDefinition(
                'vine-lash',
                'Vine Lash',
                'cantrip',
                ['druid'],
                'A spectral vine snaps from your palm and catches a nearby foe.',
                '1 action',
                '30 ft',
                'Instantaneous',
                'At will',
                'damage',
                '1d6',
                'piercing',
                null,
                false,
                true
            ),
            new ArcaneAbilityDefinition(
                'bureaucratic-hex',
                'Bureaucratic Hex',
                'cantrip',
                ['warlock'],
                'A stamped sigil of impossible paperwork strikes with eldritch force.',
                '1 action',
                '120 ft',
                'Instantaneous',
                'At will',
                'damage',
                '1d10',
                'force',
                null,
                false,
                true
            ),
            new ArcaneAbilityDefinition(
                'cutting-remark',
                'Cutting Remark',
                'cantrip',
                ['bard'],
                'A devastating observation attacks the confidence of one creature.',
                '1 action',
                '60 ft',
                'Instantaneous',
                'At will',
                'damage',
                '1d6',
                'psychic',
                'wisdom'
            ),
            new ArcaneAbilityDefinition(
                'sacred-brine',
                'Sacred Brine',
                'cantrip',
                ['cleric'],
                'A silver splash of sanctified brine burns spoilage and wickedness.',
                '1 action',
                '60 ft',
                'Instantaneous',
                'At will',
                'damage',
                '1d8',
                'radiant',
                'dexterity'
            ),
            new ArcaneAbilityDefinition(
                'restorative-preserve',
                'Restorative Preserve',
                'spell',
                ['bard', 'cleric', 'druid', 'artificer'],
                'Warm berry preserve seals cuts and restores an adventurer’s vigour.',
                '1 action',
                'Touch',
                'Instantaneous',
                '1st-level slot',
                'healing',
                '1d8',
                null,
                null,
                true,
                spellLevel: 1
            ),
            new ArcaneAbilityDefinition(
                'pantry-ward',
                'Pantry Ward',
                'spell',
                ['wizard', 'artificer'],
                'A translucent pantry-door sigil briefly protects a creature from harm.',
                '1 reaction',
                'Self',
                '1 round',
                '1st-level slot',
                spellLevel: 1
            ),
            new ArcaneAbilityDefinition(
                'market-missile',
                'Market Missile',
                'spell',
                ['wizard', 'sorcerer'],
                'Three glowing price-tags unerringly streak toward creatures you can see.',
                '1 action',
                '120 ft',
                'Instantaneous',
                '1st-level slot',
                'damage',
                '3d4',
                'force',
                spellLevel: 1
            ),
            new ArcaneAbilityDefinition(
                'fresh-stock',
                'Fresh Stock',
                'feature',
                ['grocer'],
                'Your eye for quality lets you identify useful provisions and keep the party supplied.',
                '1 minute',
                'Nearby goods',
                'Instantaneous',
                'At will'
            ),
            new ArcaneAbilityDefinition(
                'emergency-restock',
                'Emergency Restock',
                'feature',
                ['grocer'],
                'Produce exactly the snack somebody swore was packed earlier.',
                '1 bonus action',
                '30 ft',
                'Instantaneous',
                'Once per long rest',
                'healing',
                '1d6',
                null,
                null,
                true
            ),
            new ArcaneAbilityDefinition(
                'sacred-wrapping',
                'Sacred Wrapping',
                'feature',
                ['cleaver-saint'],
                'Ceremonial wrapping steadies your resolve against rot and corruption.',
                '1 bonus action',
                'Self',
                '1 minute',
                'Once per long rest'
            ),
            new ArcaneAbilityDefinition(
                'second-wind',
                'Second Wind',
                'feature',
                ['fighter'],
                'Catch your breath and pull yourself back into the fight.',
                '1 bonus action',
                'Self',
                'Instantaneous',
                'Once per short rest',
                'healing',
                '1d10',
                null,
                null,
                true
            ),
            new ArcaneAbilityDefinition(
                'rage',
                'Rage',
                'feature',
                ['barbarian'],
                'Channel a furious market-day determination into battle.',
                '1 bonus action',
                'Self',
                '1 minute',
                'Twice per long rest'
            ),
            new ArcaneAbilityDefinition(
                'sneak-attack',
                'Sneak Attack',
                'feature',
                ['rogue'],
                'Exploit a distracted foe for an extra burst of precision damage.',
                'On hit',
                'Weapon range',
                'Instantaneous',
                'Once per turn',
                'damage',
                '1d6',
                'weapon'
            ),
            new ArcaneAbilityDefinition(
                'martial-arts',
                'Martial Arts',
                'feature',
                ['monk'],
                'Your disciplined strikes turn ordinary limbs into adventuring implements.',
                'On attack',
                'Melee',
                'Instantaneous',
                'At will',
                'damage',
                '1d4',
                'bludgeoning'
            ),
            new ArcaneAbilityDefinition(
                'divine-sense',
                'Divine Sense',
                'feature',
                ['paladin'],
                'Sense strongly spoiled, sacred or profane presences nearby.',
                '1 action',
                '60 ft',
                '1 round',
                'Limited uses'
            ),
            new ArcaneAbilityDefinition(
                'favoured-mark',
                'Favoured Mark',
                'feature',
                ['ranger'],
                'Study a quarry and mark the signs needed to track it through the Marketrealm.',
                '1 bonus action',
                '90 ft',
                '1 hour',
                'Limited uses'
            ),
            new ArcaneAbilityDefinition(
                'pantry-recovery', 'Pantry Recovery', 'feature', ['wizard'],
                'During a short rest, reorganise the Arcane Pantry and recover a little spent magical stock.',
                'Short rest', 'Self', 'Instantaneous', 'Once per long rest',
                null, null, null, null, false, false, 2
            ),
            new ArcaneAbilityDefinition(
                'stocktake-instinct', 'Stocktake Instinct', 'feature', ['grocer'],
                'A rapid stocktake reveals overlooked supplies, suspicious substitutions and hidden value.',
                '1 action', '30 ft', 'Instantaneous', 'Proficiency bonus per long rest',
                null, null, null, null, false, false, 2
            ),
            new ArcaneAbilityDefinition(
                'action-surge', 'Action Surge', 'feature', ['fighter'],
                'Dig deep and take one additional action on your turn.',
                'Free', 'Self', 'Instantaneous', 'Once per short rest',
                null, null, null, null, false, false, 2
            ),
            new ArcaneAbilityDefinition(
                'sanctified-slice', 'Sanctified Slice', 'feature', ['cleaver-saint'],
                'Consecrate a strike with a ribbon of brilliant preserving light.',
                'On hit', 'Weapon range', 'Instantaneous', 'Once per long rest',
                'damage', '1d8', 'radiant', null, false, false, 2
            ),
        ];
    }

    /** @return ArcaneAbilityDefinition[] */
    public function forClass(string $class): array
    {
        return array_values(
            array_filter(
                $this->abilities,
                static fn (ArcaneAbilityDefinition $ability): bool =>
                    $ability->supportsClass($class)
            )
        );
    }
}
