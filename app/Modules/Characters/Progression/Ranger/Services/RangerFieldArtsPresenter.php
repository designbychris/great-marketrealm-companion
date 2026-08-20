<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\RangerFieldReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Player-facing active Ranger Path techniques.
 *
 * III.12.9D exposes only formulas and choices explicitly present in the
 * supplied Ranger canon. Missing damage, preparation and ammunition counts
 * are deliberately not inferred.
 */
final class RangerFieldArtsPresenter
{
    public function __construct(
        private ?RangerFieldReserveService $reserves = null
    ) {
        $this->reserves ??=
            new RangerFieldReserveService();
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
            !== 'ranger'
        ) {
            return [
                'supported' => false,
                'arts' => [],
            ];
        }

        $path = $character
            ->callingPath()
            ->value();

        if ($path === '') {
            return [
                'supported' => true,
                'path' => '',
                'path_label' =>
                    'Awaiting Ranger Path',
                'arts' => [],
                'field_reserves' => [],
            ];
        }

        $state ??=
            ActiveClassResourceState::fresh();

        $level = $character
            ->level()
            ->value();

        return [
            'supported' => true,
            'path' => $path,
            'path_label' =>
                $this->pathLabel($path),
            'arts' =>
                $this->arts(
                    $path,
                    $level
                ),
            'field_reserves' =>
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
        string $path,
        int $level
    ): array {
        $arts = match ($path) {
            'aislewarden-conclave' => [
                [
                    'key' => 'mark-the-spoor',
                    'label' => 'Mark the Spoor',
                    'level' => 3,
                    'summary' =>
                        'Mark a creature you hit as your quarry. Once per turn, deal additional quarry damage and gain advantage on Survival checks to track it.',
                    'rolls' => [[
                        'label' =>
                            'Roll Quarry Damage',
                        'formula' =>
                            $level >= 11
                                ? '1d8'
                                : '1d6',
                        'damage_type' =>
                            'weapon',
                    ]],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'slip-between-the-shelves',
                    'label' => 'Slip Between the Shelves',
                    'level' => 7,
                    'summary' =>
                        'After making an attack, move 10 feet without provoking opportunity attacks.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'relentless-pursuit',
                    'label' => 'Relentless Pursuit',
                    'level' => 11,
                    'summary' =>
                        'When your marked quarry moves willingly, use your reaction to move up to half your speed toward it.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'nowhere-left-to-run',
                    'label' => 'Nowhere Left to Run',
                    'level' => 15,
                    'summary' =>
                        'Your quarry cannot hide from you within 60 feet, and once per turn a missed attack from it can trigger an immediate weapon attack.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
            ],

            'deep-root-warden' => [
                [
                    'key' => 'grasping-roots',
                    'label' => 'Grasping Roots',
                    'level' => 3,
                    'summary' =>
                        'Once per turn after a weapon hit, force a Strength save or reduce the target’s speed to 0 until the start of your next turn.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        RangerFieldReserveService::GRASPING_ROOTS,
                ],
                [
                    'key' => 'rooted-defence',
                    'label' => 'Rooted Defence',
                    'level' => 7,
                    'summary' =>
                        'As a bonus action, root yourself until your next turn: +2 AC and immunity to forced movement and being knocked prone.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'thornroad',
                    'label' => 'Thornroad',
                    'level' => 11,
                    'summary' =>
                        'Grasping Roots creates a 10-foot area of thorny difficult terrain that harms enemies entering or starting there.',
                    'rolls' => [[
                        'label' =>
                            'Roll Thornroad Damage',
                        'formula' => '1d6',
                        'damage_type' =>
                            'piercing',
                    ]],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'heart-of-the-rootlands',
                    'label' => 'Heart of the Rootlands',
                    'level' => 15,
                    'summary' =>
                        'When reduced to 0 HP, instead drop to 1 HP, regain 2d8 + Wisdom modifier HP, and erupt restraining roots around yourself.',
                    'rolls' => [[
                        'label' =>
                            'Roll Rootlands Recovery',
                        'formula' => '2d8',
                        'damage_type' =>
                            'healing-plus-wisdom',
                        'kind' => 'healing',
                    ]],
                    'choices' => [],
                    'resource' =>
                        RangerFieldReserveService::HEART_OF_THE_ROOTLANDS,
                ],
            ],

            'cold-vault-stalker' => [
                [
                    'key' => 'frost-tipped-hunter',
                    'label' => 'Frost-Tipped Hunter',
                    'level' => 3,
                    'summary' =>
                        'Once per turn, a weapon hit deals additional cold damage.',
                    'rolls' => [[
                        'label' =>
                            'Roll Frost-Tipped Damage',
                        'formula' => '1d6',
                        'damage_type' => 'cold',
                    ]],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'flash-freeze',
                    'label' => 'Flash Freeze',
                    'level' => 7,
                    'summary' =>
                        'When a creature you can see moves within 30 feet, use your reaction to reduce its speed by 20 feet until the end of the turn.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'shattering-shot',
                    'label' => 'Shattering Shot',
                    'level' => 11,
                    'summary' =>
                        'Once per turn, hitting a creature whose speed has been reduced deals additional cold damage.',
                    'rolls' => [[
                        'label' =>
                            'Roll Shattering Damage',
                        'formula' => '2d6',
                        'damage_type' => 'cold',
                    ]],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'whiteout-hunter',
                    'label' => 'Whiteout Hunter',
                    'level' => 15,
                    'summary' =>
                        'As a bonus action, surround yourself with supernatural frost for 1 minute, obscuring you and punishing nearby enemies.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
            ],

            'conclave-of-the-forager' => [
                [
                    'key' => 'foragers-remedies',
                    'label' =>
                        'Forager’s Remedies',
                    'level' => 3,
                    'summary' =>
                        'Prepare the supplied herbal remedies after a long rest. The source does not yet define an exact preparation count.',
                    'rolls' => [],
                    'choices' => [
                        [
                            'key' =>
                                'mintleaf-draught',
                            'label' =>
                                'Mintleaf Draught',
                            'effect' =>
                                'Grants temporary HP.',
                        ],
                        [
                            'key' =>
                                'basil-balm',
                            'label' =>
                                'Basil Balm',
                            'effect' =>
                                'Ends the poisoned condition.',
                        ],
                        [
                            'key' =>
                                'rosemary-tonic',
                            'label' =>
                                'Rosemary Tonic',
                            'effect' =>
                                'Grants advantage on the next Wisdom saving throw.',
                        ],
                        [
                            'key' =>
                                'nettle-oil',
                            'label' =>
                                'Nettle Oil',
                            'effect' =>
                                'Coats a weapon to cause additional poison damage. No damage die was supplied.',
                        ],
                        [
                            'key' =>
                                'sagebrew',
                            'label' =>
                                'Sagebrew',
                            'effect' =>
                                'Adds 1d4 to the drinker’s next ability check.',
                            'formula' => '1d4',
                        ],
                    ],
                    'resource' => null,
                ],
                [
                    'key' => 'field-apothecary',
                    'label' => 'Field Apothecary',
                    'level' => 7,
                    'summary' =>
                        'Administering one of your remedies becomes a bonus action.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'potent-harvest',
                    'label' => 'Potent Harvest',
                    'level' => 11,
                    'summary' =>
                        'Damaging remedies ignore poison resistance; healing remedies restore additional HP equal to your Wisdom modifier.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'miracle-harvest',
                    'label' => 'Miracle Harvest',
                    'level' => 15,
                    'summary' =>
                        'Restore a creature to half its maximum HP and end blinded, deafened, paralyzed or poisoned.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        RangerFieldReserveService::MIRACLE_HARVEST,
                ],
            ],

            'spice-trail-hunter' => [
                [
                    'key' => 'spice-infusion',
                    'label' => 'Spice Infusion',
                    'level' => 3,
                    'summary' =>
                        'Choose an infusion when attacking. Once per turn an infused attack deals additional elemental damage.',
                    'rolls' => [],
                    'choices' => [
                        [
                            'key' => 'chilli',
                            'label' => 'Chilli',
                            'effect' =>
                                'Fire damage',
                            'formula' =>
                                $level >= 11
                                    ? '2d6'
                                    : '1d6',
                            'damage_type' =>
                                'fire',
                        ],
                        [
                            'key' => 'ginger',
                            'label' => 'Ginger',
                            'effect' =>
                                'Thunder damage',
                            'formula' =>
                                $level >= 11
                                    ? '2d6'
                                    : '1d6',
                            'damage_type' =>
                                'thunder',
                        ],
                        [
                            'key' => 'garlic',
                            'label' => 'Garlic',
                            'effect' =>
                                'Radiant damage',
                            'formula' =>
                                $level >= 11
                                    ? '2d6'
                                    : '1d6',
                            'damage_type' =>
                                'radiant',
                        ],
                        [
                            'key' =>
                                'pepperleaf',
                            'label' =>
                                'Pepperleaf',
                            'effect' =>
                                'Poison damage',
                            'formula' =>
                                $level >= 11
                                    ? '2d6'
                                    : '1d6',
                            'damage_type' =>
                                'poison',
                        ],
                    ],
                    'resource' => null,
                ],
                [
                    'key' => 'smoke-step',
                    'label' => 'Smoke Step',
                    'level' => 7,
                    'summary' =>
                        'After dealing elemental damage, move 10 feet without provoking opportunity attacks.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'extra-hot',
                    'label' => 'Extra Hot',
                    'level' => 11,
                    'summary' =>
                        'Spice Infusion increases to 2d6 and you may change infusion between attacks.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' =>
                        'the-final-seasoning',
                    'label' =>
                        'The Final Seasoning',
                    'level' => 15,
                    'summary' =>
                        'Once per long rest, empower one hit with every spice simultaneously.',
                    'rolls' => [
                        [
                            'label' =>
                                'Roll Fire Seasoning',
                            'formula' => '2d6',
                            'damage_type' => 'fire',
                        ],
                        [
                            'label' =>
                                'Roll Thunder Seasoning',
                            'formula' => '2d6',
                            'damage_type' => 'thunder',
                        ],
                        [
                            'label' =>
                                'Roll Radiant Seasoning',
                            'formula' => '2d6',
                            'damage_type' => 'radiant',
                        ],
                        [
                            'label' =>
                                'Roll Poison Seasoning',
                            'formula' => '2d6',
                            'damage_type' => 'poison',
                        ],
                    ],
                    'choices' => [],
                    'resource' =>
                        RangerFieldReserveService::FINAL_SEASONING,
                ],
            ],

            'rindrunner' => [
                [
                    'key' => 'hardened-rind',
                    'label' => 'Hardened Rind',
                    'level' => 3,
                    'summary' =>
                        'While wearing light or medium armour, gain +1 AC.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'wheyfinder',
                    'label' => 'Wheyfinder',
                    'level' => 7,
                    'summary' =>
                        'Sense creatures moving through stone or earth within 10 feet.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'piercing-wedge',
                    'label' => 'Piercing Wedge',
                    'level' => 11,
                    'summary' =>
                        'Once per turn, ignore resistance to piercing or slashing damage and deal additional damage.',
                    'rolls' => [[
                        'label' =>
                            'Roll Piercing Wedge Damage',
                        'formula' => '1d8',
                        'damage_type' =>
                            'weapon',
                    ]],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'ancient-rind',
                    'label' => 'Ancient Rind',
                    'level' => 15,
                    'summary' =>
                        'As a reaction when taking damage, halve that damage.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        RangerFieldReserveService::ANCIENT_RIND,
                ],
            ],

            'seedshot-conclave' => [
                [
                    'key' => 'seedshots',
                    'label' => 'Seedshots',
                    'level' => 3,
                    'summary' =>
                        'Choose from the supplied enchanted seed ammunition. No ammunition count or missing damage dice are inferred.',
                    'rolls' => [],
                    'choices' => [
                        [
                            'key' => 'vine-seed',
                            'label' => 'Vine Seed',
                            'effect' => 'Restrains.',
                        ],
                        [
                            'key' =>
                                'burstmelon-seed',
                            'label' =>
                                'Burstmelon Seed',
                            'effect' =>
                                'Explodes for area damage. No damage formula was supplied.',
                        ],
                        [
                            'key' => 'sunseed',
                            'label' => 'Sunseed',
                            'effect' =>
                                'Produces brilliant light and can blind.',
                        ],
                        [
                            'key' => 'heavyseed',
                            'label' => 'Heavyseed',
                            'effect' =>
                                'Knocks creatures backwards.',
                        ],
                        [
                            'key' => 'bloom-seed',
                            'label' => 'Bloom Seed',
                            'effect' =>
                                'Creates temporary healing blossoms.',
                        ],
                    ],
                    'resource' => null,
                ],
                [
                    'key' => 'ricochet-seed',
                    'label' => 'Ricochet Seed',
                    'level' => 7,
                    'summary' =>
                        'When you miss a ranged attack, redirect the projectile toward another creature within 15 feet.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' =>
                        'double-germination',
                    'label' =>
                        'Double Germination',
                    'level' => 11,
                    'summary' =>
                        'A Seedshot can affect a second creature near its original target.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'ancient-seed',
                    'label' => 'Ancient Seed',
                    'level' => 15,
                    'summary' =>
                        'Create a 30-foot-radius miniature enchanted forest for 1 minute, granting allies cover while enemies face vines and difficult terrain.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        RangerFieldReserveService::ANCIENT_SEED,
                ],
            ],

            'expiry-hunter' => [
                [
                    'key' => 'expiry-mark',
                    'label' => 'Expiry Mark',
                    'level' => 3,
                    'summary' =>
                        'Once per turn when damaging an undead, aberration or creature strongly affected by decay, deal additional radiant or necrotic damage.',
                    'rolls' => [
                        [
                            'label' =>
                                'Roll Radiant Expiry Damage',
                            'formula' => '1d8',
                            'damage_type' => 'radiant',
                        ],
                        [
                            'label' =>
                                'Roll Necrotic Expiry Damage',
                            'formula' => '1d8',
                            'damage_type' => 'necrotic',
                        ],
                    ],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'past-the-date',
                    'label' => 'Past the Date',
                    'level' => 7,
                    'summary' =>
                        'Gain poison resistance and advantage on saving throws against poison and disease.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
                ],
                [
                    'key' => 'put-it-back',
                    'label' => 'Put It Back',
                    'level' => 11,
                    'summary' =>
                        'When an undead or corrupted creature within 30 feet regains HP, use your reaction to reduce that healing to 0.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' =>
                        RangerFieldReserveService::PUT_IT_BACK,
                ],
                [
                    'key' => 'final-recall',
                    'label' => 'Final Recall',
                    'level' => 15,
                    'summary' =>
                        'Defeated undead, aberrations and corrupted creatures cannot regenerate or return through their own traits; a defeat can transfer your mark.',
                    'rolls' => [],
                    'choices' => [],
                    'resource' => null,
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

    private function pathLabel(
        string $path
    ): string {
        return match ($path) {
            'aislewarden-conclave' =>
                'Aislewarden Conclave',
            'deep-root-warden' =>
                'Deep-Root Warden',
            'cold-vault-stalker' =>
                'Cold Vault Stalker',
            'conclave-of-the-forager' =>
                'Conclave of the Forager',
            'spice-trail-hunter' =>
                'Spice Trail Hunter',
            'rindrunner' =>
                'Rindrunner',
            'seedshot-conclave' =>
                'Seedshot Conclave',
            'expiry-hunter' =>
                'Expiry Hunter',
            default =>
                ucwords(
                    str_replace(
                        '-',
                        ' ',
                        $path
                    )
                ),
        };
    }
}
