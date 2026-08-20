<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\ClericSacredReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Player-facing Cleric Calling and Divine Domain techniques.
 *
 * III.12.11D presents source-certified dice, static effects and reserve links.
 * It deliberately reuses the shared Guild Diceworks and III.12.11C reserve
 * ledger instead of introducing class-specific roll or expenditure engines.
 */
final class ClericDivineArtsPresenter
{
    public function __construct(
        private ?ClericSacredReserveService $reserves = null,
        private ?ClericSacredPolicy $policy = null
    ) {
        $this->reserves ??=
            new ClericSacredReserveService();

        $this->policy ??=
            new ClericSacredPolicy();
    }

    /** @return array<string,mixed> */
    public function present(
        Character $character,
        ?ActiveClassResourceState $state = null
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'cleric'
        ) {
            return [
                'supported' => false,
                'arts' => [],
                'sacred_reserves' => [],
            ];
        }

        $state ??=
            ActiveClassResourceState::fresh();

        $level = $character
            ->level()
            ->value();

        $domain = $character
            ->callingPath()
            ->value();

        $wisdom = $character
            ->abilityScores()
            ->wisdom()
            ->modifier();

        $saveDc = $this->policy
            ->spellSaveDc($character);

        return [
            'supported' => true,
            'domain' => $domain,
            'domain_label' =>
                $this->domainLabel($domain),
            'arts' => array_merge(
                $this->coreArts(
                    $level
                ),
                $this->domainArts(
                    $domain,
                    $level,
                    $wisdom,
                    $saveDc
                )
            ),
            'sacred_reserves' =>
                $this->reserves->reserves(
                    $character,
                    $state
                ),
        ];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function coreArts(
        int $level
    ): array {
        $arts = [];

        if ($level >= 2) {
            $arts[] = [
                'key' => 'turn-undead',
                'label' => 'Channel Divinity: Turn Undead',
                'level' => 2,
                'summary' =>
                    'Present your holy symbol and expend Channel Divinity to turn nearby undead.',
                'static' => [],
                'rolls' => [],
                'choices' => [],
                'resource' =>
                    ClericSacredReserveService::CHANNEL_DIVINITY,
                'resource_action' =>
                    'Use Turn Undead',
            ];
        }

        if ($level >= 5) {
            $threshold = match (true) {
                $level >= 17 => 'CR 4',
                $level >= 14 => 'CR 3',
                $level >= 11 => 'CR 2',
                $level >= 8 => 'CR 1',
                default => 'CR 1/2',
            };

            $arts[] = [
                'key' => 'destroy-undead',
                'label' => 'Destroy Undead',
                'level' => 5,
                'summary' =>
                    'Turn Undead destroys sufficiently weak undead that fail against your sacred turning.',
                'static' => [
                    'Current threshold' =>
                        $threshold,
                ],
                'rolls' => [],
                'choices' => [],
                'resource' => null,
            ];
        }

        if ($level >= 10) {
            $arts[] = [
                'key' => 'divine-intervention',
                'label' => 'Divine Intervention',
                'level' => 10,
                'summary' =>
                    'Call directly upon your divine power for extraordinary aid. No rest-based reserve is invented because the certified Calling source does not yet define one.',
                'static' => [
                    'Final Calling improvement' =>
                        $level >= 20
                            ? 'Reached'
                            : 'Level 20',
                ],
                'rolls' => [],
                'choices' => [],
                'resource' => null,
            ];
        }

        return $arts;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function domainArts(
        string $domain,
        int $level,
        int $wisdom,
        int $saveDc
    ): array {
        $arts = match ($domain) {
            'domain-of-sweetness' => [
                [
                    'key' => 'sweet-sanctuary',
                    'label' => 'Sweet Sanctuary',
                    'level' => 1,
                    'summary' =>
                        'Healing an ally also wraps them in sticky divine comfort for 1 minute.',
                    'static' => [
                        'Temporary HP' =>
                            (string) max(
                                0,
                                $level + $wisdom
                            ),
                        'Duration' =>
                            '1 minute or until lost',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'sugarburst',
                    'label' => 'Channel Divinity: Sugarburst',
                    'level' => 2,
                    'summary' =>
                        'Emit a 15-foot radiant sugar burst. Hostile creatures make a Dexterity save or become blinded; allies gain temporary HP and brief fear-save advantage.',
                    'static' => [
                        'Area' => '15-foot burst',
                        'Save DC' =>
                            (string) $saveDc,
                    ],
                    'rolls' => [[
                        'label' =>
                            'Roll Sugarburst Ally Temp HP',
                        'formula' => '1d6',
                        'modifier' => 0,
                        'kind' => 'healing',
                    ]],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::CHANNEL_DIVINITY,
                    'resource_action' =>
                        'Use Sugarburst',
                ],
                [
                    'key' => 'sticky-ward',
                    'label' => 'Sticky Ward',
                    'level' => 6,
                    'summary' =>
                        'Healing or buffing an ally can halve a nearby hostile creature’s speed and hinder Dexterity checks on a failed Strength save.',
                    'static' => [
                        'Trigger range' =>
                            'Hostile within 5 feet of ally',
                        'Save DC' =>
                            (string) $saveDc,
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'sticky-smite',
                    'label' => 'Divine Strike: Sticky Smite',
                    'level' => 8,
                    'summary' =>
                        'Once per turn, a weapon hit deals additional radiant syrup damage. Undead also suffer a targeting penalty.',
                    'static' => [
                        'Undead rider' =>
                            'Disadvantage attacking creatures other than you until your next turn',
                    ],
                    'rolls' => [[
                        'label' =>
                            'Roll Sticky Smite',
                        'formula' =>
                            $level >= 14
                                ? '2d8'
                                : '1d8',
                        'modifier' => 0,
                        'kind' => 'damage',
                        'damage_type' =>
                            'radiant',
                    ]],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'ascension-of-the-sugarcloud',
                    'label' => 'Ascension of the Sugarcloud',
                    'level' => 17,
                    'summary' =>
                        'Become an ethereal cotton-candy form for 1 minute using your free long-rest use or a 5th-level spell slot.',
                    'static' => [
                        'Flying speed' => '60 feet',
                        'Hover' => 'Yes',
                        'Resistance' =>
                            'All damage except force and psychic',
                        'Healing spells' =>
                            'Maximum possible healing',
                        'Hostile Wisdom-save aura' =>
                            '10 feet',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::SUGARCLOUD_ASCENSION,
                    'resource_action' =>
                        'Use Free Sugarcloud Ascension',
                ],
            ],

            'domain-of-the-golden-arches' => [
                [
                    'key' => 'divine-combo-meal',
                    'label' => 'Divine Combo Meal',
                    'level' => 1,
                    'summary' =>
                        'When you cast a healing spell on an ally, they gain +2 AC until the start of your next turn.',
                    'static' => [
                        'AC bonus' => '+2',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'order-up',
                    'label' => 'Channel Divinity: Order Up',
                    'level' => 2,
                    'summary' =>
                        'Teleport to an ally within 30 feet and immediately deliver a touch-range spell.',
                    'static' => [
                        'Teleport range' =>
                            '30 feet',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::CHANNEL_DIVINITY,
                    'resource_action' =>
                        'Use Order Up',
                ],
                [
                    'key' => 'express-blessing',
                    'label' => 'Express Blessing',
                    'level' => 6,
                    'summary' =>
                        'Once per turn, an ally you heal or buff may immediately move 10 feet without provoking opportunity attacks.',
                    'static' => [
                        'Free movement' =>
                            '10 feet',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'golden-fry-strike',
                    'label' => 'Divine Strike: Golden Fry',
                    'level' => 8,
                    'summary' =>
                        'Once per turn, a weapon hit deals additional radiant damage.',
                    'static' => [],
                    'rolls' => [[
                        'label' =>
                            'Roll Golden Fry Strike',
                        'formula' =>
                            $level >= 14
                                ? '2d8'
                                : '1d8',
                        'modifier' => 0,
                        'kind' => 'damage',
                        'damage_type' =>
                            'radiant',
                    ]],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'happy-heal-hour',
                    'label' => 'Happy Heal Hour',
                    'level' => 17,
                    'summary' =>
                        'For 1 minute, your healing spells restore the maximum possible amount instead of rolling.',
                    'static' => [
                        'Duration' => '1 minute',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::HAPPY_HEAL_HOUR,
                    'resource_action' =>
                        'Start Happy Heal Hour',
                ],
            ],

            'domain-of-dairy' => [
                [
                    'key' => 'dairy-domain-magic',
                    'label' => 'Dairy Domain Magic',
                    'level' => 1,
                    'summary' =>
                        'Grease and the Dairy Domain spell table become part of your creamy sacred repertoire.',
                    'static' => [],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'curdled-blessing',
                    'label' => 'Channel Divinity: Curdled Blessing',
                    'level' => 2,
                    'summary' =>
                        'Create a 10-foot sticky aura for 1 minute: allies gain +1 AC and enemies treat the area as difficult terrain.',
                    'static' => [
                        'Area' => '10-foot radius',
                        'Duration' => '1 minute',
                        'Ally AC' => '+1',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::CHANNEL_DIVINITY,
                    'resource_action' =>
                        'Use Curdled Blessing',
                ],
                [
                    'key' => 'stinky-salvation',
                    'label' => 'Stinky Salvation',
                    'level' => 6,
                    'summary' =>
                        'Allow yourself or an ally within 30 feet to reroll a failed save against poison or disease.',
                    'static' => [
                        'Range' => '30 feet',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::STINKY_SALVATION,
                    'resource_action' =>
                        'Use Stinky Salvation',
                ],
                [
                    'key' => 'cultured-smite',
                    'label' => 'Divine Strike: Cultured Smite',
                    'level' => 8,
                    'summary' =>
                        'Once per turn, a weapon hit deals additional radiant or cold damage.',
                    'static' => [],
                    'rolls' => [
                        [
                            'label' =>
                                'Roll Cultured Smite — Radiant',
                            'formula' =>
                                $level >= 14
                                    ? '2d8'
                                    : '1d8',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'radiant',
                        ],
                        [
                            'label' =>
                                'Roll Cultured Smite — Cold',
                            'formula' =>
                                $level >= 14
                                    ? '2d8'
                                    : '1d8',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'cold',
                        ],
                    ],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'holy-butterstorm',
                    'label' => 'Holy Butterstorm',
                    'level' => 17,
                    'summary' =>
                        'Call down burning clarified butter in a 30-foot radius. Enemies make a Dexterity save or fall prone and suffer radiant plus fire damage.',
                    'static' => [
                        'Area' =>
                            '30-foot radius',
                        'Save DC' =>
                            (string) $saveDc,
                        'Failed-save rider' =>
                            'Fall prone',
                    ],
                    'rolls' => [
                        [
                            'label' =>
                                'Roll Holy Butterstorm Radiant',
                            'formula' => '6d8',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'radiant',
                        ],
                        [
                            'label' =>
                                'Roll Holy Butterstorm Fire',
                            'formula' => '2d8',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'fire',
                        ],
                    ],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::HOLY_BUTTERSTORM,
                    'resource_action' =>
                        'UNLEASH HOLY BUTTERSTORM',
                    'celebratory' => true,
                ],
            ],

            'domain-of-seasoning' => [
                [
                    'key' => 'flavourful-touch',
                    'label' => 'Flavourful Touch & Zest',
                    'level' => 1,
                    'summary' =>
                        'Flavour or ruin food and water at will; Zest a weapon for +1 to attack and damage rolls for 1 hour.',
                    'static' => [
                        'Zest bonus' =>
                            '+1 attack and damage',
                        'Duration' =>
                            '1 hour',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::ZEST,
                    'resource_action' =>
                        'Zest a Weapon',
                ],
                [
                    'key' => 'salt-the-earth',
                    'label' => 'Channel Divinity: Salt the Earth',
                    'level' => 2,
                    'summary' =>
                        'Consecrate a 15-foot-radius area for 1 minute, suppressing hostile healing, regeneration and potion benefits.',
                    'static' => [
                        'Area' =>
                            '15-foot radius',
                        'Duration' =>
                            '1 minute',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::CHANNEL_DIVINITY,
                    'resource_action' =>
                        'Salt the Earth',
                ],
                [
                    'key' => 'searing-seasoning',
                    'label' => 'Searing Seasoning',
                    'level' => 6,
                    'summary' =>
                        'When a creature fails a save against one of your fire, poison or acid spells, deal an extra 1d8 of that damage type.',
                    'static' => [],
                    'rolls' => [
                        [
                            'label' =>
                                'Roll Searing Fire',
                            'formula' => '1d8',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'fire',
                        ],
                        [
                            'label' =>
                                'Roll Searing Poison',
                            'formula' => '1d8',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'poison',
                        ],
                        [
                            'label' =>
                                'Roll Searing Acid',
                            'formula' => '1d8',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'acid',
                        ],
                    ],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'seasoned-divine-strike',
                    'label' => 'Divine Strike: Seasoned Edge',
                    'level' => 8,
                    'summary' =>
                        'Once per turn, a weapon hit deals additional fire or poison damage.',
                    'static' => [],
                    'rolls' => [
                        [
                            'label' =>
                                'Roll Seasoned Edge — Fire',
                            'formula' =>
                                $level >= 14
                                    ? '2d8'
                                    : '1d8',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'fire',
                        ],
                        [
                            'label' =>
                                'Roll Seasoned Edge — Poison',
                            'formula' =>
                                $level >= 14
                                    ? '2d8'
                                    : '1d8',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'poison',
                        ],
                    ],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'perfect-balance',
                    'label' => 'Perfect Balance',
                    'level' => 17,
                    'summary' =>
                        'For 1 minute, fire, poison and acid damage from your Cleric spells ignores resistance. Immunity remains intact.',
                    'static' => [
                        'Duration' => '1 minute',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::PERFECT_BALANCE,
                    'resource_action' =>
                        'Invoke Perfect Balance',
                ],
            ],

            'domain-of-cultivation' => [
                [
                    'key' => 'cultivator-proficiencies',
                    'label' => 'Cultivator Proficiencies',
                    'level' => 1,
                    'summary' =>
                        'Gain proficiency in Nature and brewer’s supplies.',
                    'static' => [
                        'Proficiencies' =>
                            'Nature · brewer’s supplies',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'blessed-brine',
                    'label' => 'Channel Divinity: Blessed Brine',
                    'level' => 2,
                    'summary' =>
                        'Create a 15-foot briny aura for 1 minute. Undead suffer disadvantaged saves and allies regain HP at the start of their turns.',
                    'static' => [
                        'Area' =>
                            '15-foot radius',
                        'Duration' =>
                            '1 minute',
                    ],
                    'rolls' => [[
                        'label' =>
                            'Roll Blessed Brine Healing',
                        'formula' => '1d6',
                        'modifier' => 0,
                        'kind' => 'healing',
                    ]],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::CHANNEL_DIVINITY,
                    'resource_action' =>
                        'Use Blessed Brine',
                ],
                [
                    'key' => 'patient-culture',
                    'label' => 'Patient Culture',
                    'level' => 6,
                    'summary' =>
                        'An ally healed by one of your Cleric spells gains advantage on its next poison or disease saving throw before the end of its next turn.',
                    'static' => [],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'cultivated-potency',
                    'label' => 'Potent Spellcasting: Cultivated Faith',
                    'level' => 8,
                    'summary' =>
                        'Add your Wisdom modifier to the damage dealt by any Cleric cantrip.',
                    'static' => [
                        'Current Wisdom bonus' =>
                            sprintf('%+d', $wisdom),
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'sacred-vintage',
                    'label' => 'Sacred Vintage',
                    'level' => 17,
                    'summary' =>
                        'Create a 30-foot restorative aura for 1 minute that protects against poison and disease and strengthens your healing.',
                    'static' => [
                        'Area' =>
                            '30-foot aura',
                        'Duration' =>
                            '1 minute',
                        'Healing bonus' =>
                            sprintf(
                                '%+d to one healing roll',
                                $wisdom
                            ),
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        ClericSacredReserveService::SACRED_VINTAGE,
                    'resource_action' =>
                        'Begin Sacred Vintage',
                ],
            ],

            'domain-of-fermentation' => [
                [
                    'key' => 'ferment-touch',
                    'label' => 'Ferment Touch',
                    'level' => 1,
                    'summary' =>
                        'Channel microbial magic into a nearby ally, corpse or enemy.',
                    'static' => [
                        'Range' => '5 feet',
                        'Uses' =>
                            sprintf(
                                '%d per long rest',
                                max(1, $wisdom)
                            ),
                        'Ally limit' =>
                            'Once per creature per long rest',
                    ],
                    'rolls' => [],
                    'choices' => [
                        [
                            'key' =>
                                'ferment-touch-heal',
                            'label' =>
                                'Ferment Touch — Heal Ally',
                            'effect' =>
                                'The ally regains 1d8 + Wisdom modifier HP.',
                            'formula' => '1d8',
                            'modifier' => $wisdom,
                            'kind' => 'healing',
                        ],
                        [
                            'key' =>
                                'ferment-touch-preserve',
                            'label' =>
                                'Ferment Touch — Preserve Corpse',
                            'effect' =>
                                'Preserve a corpse indefinitely with a faint garlic-sour aroma.',
                        ],
                        [
                            'key' =>
                                'ferment-touch-sour',
                            'label' =>
                                'Ferment Touch — Sour Enemy',
                            'effect' =>
                                'On a failed Constitution save, deal acid damage and impose disadvantage on the next attack.',
                            'formula' =>
                                match (true) {
                                    $level >= 17 => '4d8',
                                    $level >= 11 => '3d8',
                                    $level >= 5 => '2d8',
                                    default => '1d8',
                                },
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'acid',
                            'save_dc' =>
                                $saveDc,
                        ],
                    ],
                    'resource' =>
                        ClericSacredReserveService::FERMENT_TOUCH,
                    'resource_action' =>
                        'Spend Ferment Touch',
                ],
                [
                    'key' => 'funk-of-the-divine',
                    'label' => 'Channel Divinity: Funk of the Divine',
                    'level' => 2,
                    'summary' =>
                        'Create a 15-foot fermentation aura for 1 minute. Enemies risk radiant or poison damage while allies gain a d4 sacred fermentation bonus.',
                    'static' => [
                        'Area' =>
                            '15-foot radius',
                        'Duration' =>
                            '1 minute · concentration',
                        'Save DC' =>
                            (string) $saveDc,
                    ],
                    'rolls' => [[
                        'label' =>
                            'Roll Funk Ally d4 Bonus',
                        'formula' => '1d4',
                        'modifier' => 0,
                        'kind' => 'support',
                    ]],
                    'choices' => [
                        [
                            'key' =>
                                'funk-radiant',
                            'label' =>
                                'Funk Damage — Radiant',
                            'effect' =>
                                'On a failed Constitution save, deal 2d10 + Cleric level radiant damage.',
                            'formula' => '2d10',
                            'modifier' => $level,
                            'kind' => 'damage',
                            'damage_type' =>
                                'radiant',
                        ],
                        [
                            'key' =>
                                'funk-poison',
                            'label' =>
                                'Funk Damage — Poison',
                            'effect' =>
                                'On a failed Constitution save, deal 2d10 + Cleric level poison damage.',
                            'formula' => '2d10',
                            'modifier' => $level,
                            'kind' => 'damage',
                            'damage_type' =>
                                'poison',
                        ],
                    ],
                    'resource' =>
                        ClericSacredReserveService::CHANNEL_DIVINITY,
                    'resource_action' =>
                        'Release Funk of the Divine',
                ],
                [
                    'key' => 'spiritual-brine',
                    'label' => 'Spiritual Brine',
                    'level' => 6,
                    'summary' =>
                        'Gain acid and poison resistance, poisoned immunity, and once per round may react to turn nearby poison damage into healing.',
                    'static' => [
                        'Resistances' =>
                            'Acid · poison',
                        'Immunity' =>
                            'Poisoned condition',
                        'Reaction range' =>
                            '30 feet',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'pickled-spirits',
                    'label' => 'Pickled Spirits',
                    'level' => 8,
                    'summary' =>
                        'Every target healed by one of your spells also gains temporary HP equal to your Wisdom modifier.',
                    'static' => [
                        'Temporary HP' =>
                            (string) max(
                                0,
                                $wisdom
                            ),
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'mother-culture',
                    'label' => 'Mother Culture',
                    'level' => 17,
                    'summary' =>
                        'Summon the glowing Mother Culture for 1 minute. It heals allies, sheds conditions and punishes hostile creatures within 30 feet.',
                    'static' => [
                        'Area' =>
                            '30 feet',
                        'Duration' =>
                            '1 minute',
                        'Conditions shed' =>
                            'Poisoned · blinded · deafened · frightened · paralyzed',
                        'Save DC' =>
                            (string) $saveDc,
                    ],
                    'rolls' => [[
                        'label' =>
                            'Roll Mother Culture Ally Healing',
                        'formula' => '2d6',
                        'modifier' => 0,
                        'kind' => 'healing',
                    ]],
                    'choices' => [
                        [
                            'key' =>
                                'mother-radiant',
                            'label' =>
                                'Mother Culture — Radiant',
                            'effect' =>
                                'On a failed Constitution save, deal radiant damage and halve speed until the next turn.',
                            'formula' => '4d6',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'radiant',
                        ],
                        [
                            'key' =>
                                'mother-poison',
                            'label' =>
                                'Mother Culture — Poison',
                            'effect' =>
                                'On a failed Constitution save, deal poison damage and halve speed until the next turn.',
                            'formula' => '4d6',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'poison',
                        ],
                    ],
                    'resource' =>
                        ClericSacredReserveService::MOTHER_CULTURE,
                    'resource_action' =>
                        'Summon Mother Culture',
                ],
            ],

            default => [],
        };

        return array_values(
            array_filter(
                $arts,
                static fn (
                    array $art
                ): bool =>
                    (int) (
                        $art['level']
                        ?? 99
                    ) <= $level
            )
        );
    }

    private function domainLabel(
        string $domain
    ): string {
        return match ($domain) {
            'domain-of-sweetness' =>
                'Domain of Sweetness',
            'domain-of-the-golden-arches' =>
                'Domain of the Golden Arches',
            'domain-of-dairy' =>
                'Domain of Dairy',
            'domain-of-seasoning' =>
                'Domain of Seasoning',
            'domain-of-cultivation' =>
                'Domain of Cultivation',
            'domain-of-fermentation' =>
                'Domain of Fermentation',
            '' => 'Domain not yet chosen',
            default =>
                ucwords(
                    str_replace(
                        '-',
                        ' ',
                        $domain
                    )
                ),
        };
    }
}
