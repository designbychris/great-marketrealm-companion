<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\DruidPrimalReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Player-facing Druid Circle techniques for III.12.10D.
 *
 * Rolls, static values and resource links are limited to mechanics explicitly
 * supplied by the Great Marketrealm Circle source.
 */
final class DruidGroveArtsPresenter
{
    public function __construct(
        private ?DruidPrimalReserveService $reserves = null
    ) {
        $this->reserves ??=
            new DruidPrimalReserveService();
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
            !== 'druid'
        ) {
            return [
                'supported' => false,
                'arts' => [],
            ];
        }

        $circle = $character
            ->callingPath()
            ->value();

        $state ??=
            ActiveClassResourceState::fresh();

        if ($circle === '') {
            return [
                'supported' => true,
                'circle' => '',
                'circle_label' =>
                    'Circle not yet chosen',
                'arts' => [],
                'primal_reserves' =>
                    $this->reserves->reserves(
                        $character,
                        $state
                    ),
            ];
        }

        $level = $character
            ->level()
            ->value();

        $wisdom = $character
            ->abilityScores()
            ->wisdom()
            ->modifier();

        $pb = $character
            ->proficiencyBonus()
            ->value();

        return [
            'supported' => true,
            'circle' => $circle,
            'circle_label' =>
                $this->circleLabel($circle),
            'arts' =>
                $this->arts(
                    $circle,
                    $level,
                    $wisdom,
                    $pb
                ),
            'primal_reserves' =>
                $this->reserves->reserves(
                    $character,
                    $state
                ),
        ];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function arts(
        string $circle,
        int $level,
        int $wisdom,
        int $pb
    ): array {
        $arts = match ($circle) {
            'circle-of-eating-fresh' => [
                [
                    'key' => 'crisp-aura',
                    'label' => 'Crisp Aura',
                    'level' => 2,
                    'summary' =>
                        'Allies within 10 feet regain 1 HP per round while standing in natural terrain.',
                    'static' => [
                        'Natural-terrain healing' =>
                            '1 HP per round',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::CRISP_AURA_EXPANSION,
                    'resource_action' =>
                        'Expand aura to 30 feet for 1 minute',
                ],
                [
                    'key' => 'natures-label',
                    'label' => 'Nature’s Label',
                    'level' => 6,
                    'summary' =>
                        'Inspect an item or creature for organic purity, disease, spoilage or magical taint. Detect Poison and Disease is always prepared.',
                    'static' => [],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'hydroponic-revival',
                    'label' => 'Hydroponic Revival',
                    'level' => 10,
                    'summary' =>
                        'Cure Wounds also grants advantage on the target’s next attack or saving throw.',
                    'static' => [],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'preservative-purge',
                    'label' => 'Preservative Purge',
                    'level' => 14,
                    'summary' =>
                        'Unleash a 30-foot cleansing storm that purges toxins, conditions and corruption.',
                    'static' => [],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::PRESERVATIVE_PURGE,
                    'resource_action' =>
                        'Unleash Preservative Purge',
                ],
            ],

            'circle-of-the-groveflame' => [
                [
                    'key' => 'spiceburst',
                    'label' => 'Spiceburst',
                    'level' => 2,
                    'summary' =>
                        'When a spell deals fire damage, add your Wisdom modifier to one damage roll.',
                    'static' => [
                        'Current Wisdom bonus' =>
                            sprintf('%+d', $wisdom),
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'spiceberry',
                    'label' => 'Spiceberry',
                    'level' => 6,
                    'summary' =>
                        'Each Goodberry also grants 1 temporary HP and frightened immunity for 1 hour.',
                    'static' => [
                        'Temporary HP per berry' => '1',
                        'Frightened immunity' => '1 hour',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'flame-frond-form',
                    'label' => 'Flame Frond Form',
                    'level' => 10,
                    'summary' =>
                        'Wild Shape may become a Spice Basilisk using the DM’s stat block.',
                    'static' => [],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::WILD_SHAPE,
                    'resource_action' =>
                        'Spend Wild Shape for Spice Basilisk',
                    'secondary_resource' =>
                        DruidPrimalReserveService::SPICE_BASILISK_BREATH,
                    'secondary_action' =>
                        'Use Spicy Breath',
                ],
                [
                    'key' => 'scorching-bloom',
                    'label' => 'Scorching Bloom',
                    'level' => 14,
                    'summary' =>
                        'Erupt spice flowers in 20 feet. Enemies take fire damage and may be stunned.',
                    'static' => [
                        'Area' => '20 feet',
                    ],
                    'rolls' => [[
                        'label' =>
                            'Roll Scorching Bloom Damage',
                        'formula' => '4d8',
                        'modifier' => 0,
                        'kind' => 'damage',
                        'damage_type' => 'fire',
                    ]],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::SCORCHING_BLOOM,
                    'resource_action' =>
                        'Use Scorching Bloom',
                    'secondary_resource' =>
                        DruidPrimalReserveService::PUNGENT_FLAME,
                    'secondary_action' =>
                        'Use Pungent Flame',
                ],
            ],

            'circle-of-the-deep-soil' => [
                [
                    'key' => 'buried-memory',
                    'label' => 'Buried Memory',
                    'level' => 2,
                    'summary' =>
                        'Speak with dead plants and recall the last 24 hours remembered by a tree or root system.',
                    'static' => [
                        'Memory window' => '24 hours',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'earthen-hold',
                    'label' => 'Earthen Hold',
                    'level' => 6,
                    'summary' =>
                        'React to erupt vines and reduce a creature’s speed to 0 on a failed Constitution save.',
                    'static' => [
                        'Failed-save speed' => '0',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'soil-communion',
                    'label' => 'Soil Communion',
                    'level' => 10,
                    'summary' =>
                        'No longer require food or water and become immune to petrified and restrained.',
                    'static' => [],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'living-earthquake',
                    'label' => 'Living Earthquake',
                    'level' => 14,
                    'summary' =>
                        'Create a 20-foot tremor for 1 minute. Creatures face the supplied fixed Dexterity save each round or fall prone.',
                    'static' => [
                        'Area' => '20 feet',
                        'Duration' => '1 minute',
                        'Dexterity save' => 'DC 16',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::LIVING_EARTHQUAKE,
                    'resource_action' =>
                        'Use Living Earthquake',
                ],
            ],

            'circle-of-the-compost' => [
                [
                    'key' => 'rotbound-affinity',
                    'label' => 'Rotbound Affinity',
                    'level' => 2,
                    'summary' =>
                        'Resist poison and necrotic damage, commune with vermin and fungi, and gain Druidcraft and Infestation if needed.',
                    'static' => [
                        'Resistances' =>
                            'Poison & necrotic',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'compost-surge',
                    'label' => 'Compost Surge',
                    'level' => 2,
                    'summary' =>
                        'When a creature within 30 feet drops to 0 HP, react with either restorative compost or necrotic recycling.',
                    'static' => [
                        'Trigger range' => '30 feet',
                        'Necrotic target range' => '10 feet',
                    ],
                    'rolls' => [],
                    'choices' => [
                        [
                            'key' => 'reclaim-vitality',
                            'label' => 'Reclaim Vitality',
                            'effect' =>
                                'Regain 1d6 + Wisdom modifier HP.',
                            'formula' => '1d6',
                            'modifier' => $wisdom,
                            'kind' => 'healing',
                        ],
                        [
                            'key' => 'recycle-into-harm',
                            'label' => 'Recycle into Harm',
                            'effect' =>
                                'Another creature within 10 feet of the fallen target takes necrotic damage equal to your Druid level.',
                            'static_value' =>
                                (string) $level,
                            'static_suffix' =>
                                ' necrotic damage',
                        ],
                    ],
                    'resource' =>
                        DruidPrimalReserveService::COMPOST_SURGE,
                    'resource_action' =>
                        'Spend Compost Surge',
                ],
                [
                    'key' => 'mulchborn',
                    'label' => 'Mulchborn',
                    'level' => 6,
                    'summary' =>
                        'Root yourself for half cover and forced-movement immunity; adjacent creatures risk poison damage.',
                    'static' => [
                        'Save DC' =>
                            (string) (
                                8 + $wisdom + $pb
                            ),
                        'Duration' =>
                            '1 minute or until you move',
                    ],
                    'rolls' => [[
                        'label' =>
                            'Roll Mulchborn Poison Damage',
                        'formula' => '2d8',
                        'modifier' => 0,
                        'kind' => 'damage',
                        'damage_type' => 'poison',
                    ]],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::MULCHBORN,
                    'resource_action' =>
                        'Use Mulchborn',
                ],
                [
                    'key' => 'bloom-of-decay',
                    'label' => 'Bloom of Decay',
                    'level' => 10,
                    'summary' =>
                        'Create a 20-foot compost bloom for 1 minute: difficult terrain, poison for enemies and regeneration for allies.',
                    'static' => [
                        'Area' => '20-foot radius',
                        'Duration' => '1 minute',
                    ],
                    'rolls' => [
                        [
                            'label' =>
                                'Roll Bloom Poison Damage',
                            'formula' => '4d6',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' => 'poison',
                        ],
                        [
                            'label' =>
                                'Roll Bloom Ally Healing',
                            'formula' => '1d6',
                            'modifier' => 0,
                            'kind' => 'healing',
                        ],
                    ],
                    'choices' => [
                        [
                            'key' => 'blight',
                            'label' => 'Blight',
                            'effect' =>
                                'Cast once per long rest without using a spell slot.',
                            'resource' =>
                                DruidPrimalReserveService::BLIGHT,
                        ],
                        [
                            'key' => 'insect-plague',
                            'label' => 'Insect Plague',
                            'effect' =>
                                'Cast once per long rest without using a spell slot.',
                            'resource' =>
                                DruidPrimalReserveService::INSECT_PLAGUE,
                        ],
                    ],
                    'resource' =>
                        DruidPrimalReserveService::BLOOM_OF_DECAY,
                    'resource_action' =>
                        'Use Bloom of Decay',
                ],
                [
                    'key' => 'avatar-of-the-rotten-grove',
                    'label' => 'Avatar of the Rotten Grove',
                    'level' => 14,
                    'summary' =>
                        'Spend Wild Shape to become a Large Compost Elemental for 1 minute.',
                    'static' => [
                        'Temporary HP' =>
                            (string) (2 * $level),
                        'AC bonus' => '+2',
                        'Mulch Slam attacks' => '2',
                        'Aura' => '10 feet',
                    ],
                    'rolls' => [
                        [
                            'label' =>
                                'Roll Mulch Slam Bludgeoning',
                            'formula' => '2d10',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' =>
                                'bludgeoning',
                        ],
                        [
                            'label' =>
                                'Roll Mulch Slam Poison',
                            'formula' => '2d6',
                            'modifier' => 0,
                            'kind' => 'damage',
                            'damage_type' => 'poison',
                        ],
                    ],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::WILD_SHAPE,
                    'resource_action' =>
                        'Spend Wild Shape for Compost Elemental',
                ],
            ],

            'circle-of-curdle' => [
                [
                    'key' => 'spoilage-touch',
                    'label' => 'Spoilage Touch & Aura of Curdling',
                    'level' => 2,
                    'summary' =>
                        'Gain Infestation and Chill Touch, spoil or preserve food and water, and impose Constitution-save disadvantage within 5 feet while Wild Shaped.',
                    'static' => [
                        'Curdling aura' => '5 feet',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'rot-within',
                    'label' => 'Rot Within',
                    'level' => 6,
                    'summary' =>
                        'A creature failing a save against your Druid spell becomes Curdled: -1 AC and disadvantage on one saving throw chosen each round.',
                    'static' => [
                        'AC penalty' => '-1',
                        'Duration' => 'Up to 1 minute',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'animate-spoil',
                    'label' => 'Animate Spoil',
                    'level' => 10,
                    'summary' =>
                        'Animate a 10-foot-radius patch of rotting food into an allied CR 4 Curd Golem.',
                    'static' => [
                        'Patch' => '10-foot radius',
                        'Golem' => 'CR 4',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::ANIMATE_SPOIL,
                    'resource_action' =>
                        'Animate Curd Golem',
                ],
                [
                    'key' => 'bacteria-bloom',
                    'label' => 'Bacteria Bloom',
                    'level' => 14,
                    'summary' =>
                        'Release beneficial spores: allies gain temporary HP and enemies save or become poisoned for 1 minute.',
                    'static' => [
                        'Area' => '20 feet',
                        'Ally temporary HP' =>
                            (string) ($level + $wisdom),
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
            ],

            'circle-of-the-churn' => [
                [
                    'key' => 'frozen-curd',
                    'label' => 'Frozen Curd',
                    'level' => 2,
                    'summary' =>
                        'Enter a 10-minute frost-covered Curd Form with fire resistance, ice/snow mobility and cold unarmed attacks.',
                    'static' => [
                        'Duration' => '10 minutes',
                        'Resistance' => 'Fire',
                    ],
                    'rolls' => [],
                    'choices' => [
                        [
                            'key' => 'free-curd-form',
                            'label' => 'Free Curd Form',
                            'effect' =>
                                'Use the once-per-long-rest free Curd Form.',
                            'resource' =>
                                DruidPrimalReserveService::FROZEN_CURD,
                        ],
                        [
                            'key' => 'wild-shape-curd-form',
                            'label' => 'Wild Shape Curd Form',
                            'effect' =>
                                'Enter Curd Form by spending Wild Shape.',
                            'resource' =>
                                DruidPrimalReserveService::WILD_SHAPE,
                        ],
                    ],
                    'resource' => null,
                ],
                [
                    'key' => 'blessing-of-the-creammother',
                    'label' => 'Blessing of the Creammother',
                    'level' => 6,
                    'summary' =>
                        'Healing spells also grant temporary HP and resistance to the next source of fire or necrotic damage.',
                    'static' => [],
                    'rolls' => [[
                        'label' =>
                            'Roll Creammother Temporary HP',
                        'formula' => '1d6',
                        'modifier' => 0,
                        'kind' => 'healing',
                    ]],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'glacial-growth',
                    'label' => 'Glacial Growth',
                    'level' => 10,
                    'summary' =>
                        'Create a 10-foot icy difficult-terrain zone within 60 feet for 1 minute; enemies initially save against being knocked prone.',
                    'static' => [
                        'Area' => '10-foot radius',
                        'Range' => '60 feet',
                        'Duration' => '1 minute',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::GLACIAL_GROWTH,
                    'resource_action' =>
                        'Use Glacial Growth',
                ],
                [
                    'key' => 'true-churnform',
                    'label' => 'True Churnform',
                    'level' => 14,
                    'summary' =>
                        'Become primordial dairy elemental power for 1 minute; gain recurring temporary HP and maximize healing and cold spell results.',
                    'static' => [
                        'Duration' => '1 minute',
                        'Temp HP each turn' =>
                            (string) $level,
                        'Healing/cold spells' =>
                            'Maximized',
                    ],
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        DruidPrimalReserveService::TRUE_CHURNFORM,
                    'resource_action' =>
                        'Enter True Churnform',
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

    private function circleLabel(
        string $circle
    ): string {
        return match ($circle) {
            'circle-of-eating-fresh' =>
                'Circle of Eating Fresh',
            'circle-of-the-groveflame' =>
                'Circle of the Groveflame',
            'circle-of-the-deep-soil' =>
                'Circle of the Deep Soil',
            'circle-of-the-compost' =>
                'Circle of the Compost',
            'circle-of-curdle' =>
                'Circle of Curdle',
            'circle-of-the-churn' =>
                'Circle of the Churn',
            default =>
                ucwords(
                    str_replace(
                        '-',
                        ' ',
                        $circle
                    )
                ),
        };
    }
}
