<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Canon Ranger Path gifts from III.12.9B.
 *
 * This slice certifies the user's 3 / 7 / 11 / 15 Path feature cadence.
 * Later Ranger slices may add interactive resource expenditure for features
 * such as remedies, infusions, Seedshots and proficiency-based uses.
 */
final class RangerPathGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /** @var array<string,array{label:string,gifts:array<int,array<string,mixed>>}> */
    private const PATHS = [
        'aislewarden-conclave' => [
            'label' => 'Aislewarden Conclave',
            'gifts' => [
                [
                    'key' => 'mark-the-spoor-and-market-strider',
                    'label' => 'Mark the Spoor & Market Strider',
                    'level' => 3,
                    'summary' => 'Mark a creature you hit as your quarry for +1d6 damage once per turn and easier tracking; ignore specified forms of difficult terrain.',
                    'detail' => 'Mark the Spoor grants 1d6 additional damage once per turn against the marked quarry and advantage on Survival checks to track it. Market Strider means difficult terrain caused by plants, discarded goods, shelving, rubble or crowds does not cost additional movement.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'slip-between-the-shelves',
                    'label' => 'Slip Between the Shelves',
                    'level' => 7,
                    'summary' => 'After making an attack, move 10 feet without provoking opportunity attacks.',
                    'detail' => 'After making an attack, you can move 10 feet without provoking opportunity attacks.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'relentless-pursuit',
                    'label' => 'Relentless Pursuit',
                    'level' => 11,
                    'summary' => 'React to a marked quarry moving willingly by pursuing it; quarry damage becomes 1d8.',
                    'detail' => 'When your marked quarry moves willingly, you may use your reaction to move up to half your speed toward it. Your quarry damage also becomes 1d8.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'nowhere-left-to-run',
                    'label' => 'Nowhere Left to Run',
                    'level' => 15,
                    'summary' => 'Your quarry cannot hide nearby, and its missed attacks can open an immediate counterattack.',
                    'detail' => 'Your marked quarry cannot become hidden from you while within 60 feet. Once per turn when it misses you with an attack, you may immediately make one weapon attack against it.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'deep-root-warden' => [
            'label' => 'Deep-Root Warden',
            'gifts' => [
                [
                    'key' => 'grasping-roots-and-rootspeaker',
                    'label' => 'Grasping Roots & Rootspeaker',
                    'level' => 3,
                    'summary' => 'Weapon hits can root a target in place, while Rootspeaker grants druidcraft and simple communication with mundane plants.',
                    'detail' => 'Once per turn when you hit with a weapon attack, you can cause roots to erupt around the target. It must make a Strength save or have speed 0 until the start of your next turn. Uses equal your proficiency bonus per long rest. You also learn druidcraft and can communicate simple concepts with mundane plants.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'rooted-defence',
                    'label' => 'Rooted Defence',
                    'level' => 7,
                    'summary' => 'Root yourself briefly for +2 AC and immunity to forced movement and being knocked prone.',
                    'detail' => 'As a bonus action, root yourself until the beginning of your next turn. You cannot be forcibly moved or knocked prone and gain +2 AC.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'thornroad',
                    'label' => 'Thornroad',
                    'level' => 11,
                    'summary' => 'Grasping Roots creates thorny difficult terrain that damages enemies.',
                    'detail' => 'When you use Grasping Roots, a 10-foot area around the target becomes thorny difficult terrain. Enemies entering it or starting their turn there take 1d6 piercing damage.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'heart-of-the-rootlands',
                    'label' => 'Heart of the Rootlands',
                    'level' => 15,
                    'summary' => 'Once per long rest, survive a fall to 0 HP, recover, and erupt restraining roots around yourself.',
                    'detail' => 'Once per long rest, when reduced to 0 HP, you instead drop to 1 HP, regain 2d8 + your Wisdom modifier HP, and roots erupt around you, restraining nearby enemies that fail a Strength save.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'cold-vault-stalker' => [
            'label' => 'Cold Vault Stalker',
            'gifts' => [
                [
                    'key' => 'frost-tipped-hunter-and-cold-vault-born',
                    'label' => 'Frost-Tipped Hunter & Cold Vault Born',
                    'level' => 3,
                    'summary' => 'Add 1d6 cold damage once per turn, resist cold damage and ignore ice-and-snow movement penalties.',
                    'detail' => 'Once per turn, a weapon hit deals an additional 1d6 cold damage. You gain resistance to cold damage and ignore movement penalties caused by ice and snow.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'flash-freeze',
                    'label' => 'Flash Freeze',
                    'level' => 7,
                    'summary' => 'Use your reaction to cut the speed of a moving creature within 30 feet by 20 feet.',
                    'detail' => 'When a creature you can see moves within 30 feet, use your reaction to reduce its speed by 20 feet until the end of the turn.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'shattering-shot',
                    'label' => 'Shattering Shot',
                    'level' => 11,
                    'summary' => 'Once per turn, hit a slowed creature for another 2d6 cold damage.',
                    'detail' => 'Once per turn, hitting a creature whose speed has been reduced deals another 2d6 cold damage.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'whiteout-hunter',
                    'label' => 'Whiteout Hunter',
                    'level' => 15,
                    'summary' => 'Create a one-minute supernatural whiteout that obscures you, hinders distant attacks and chills nearby enemies.',
                    'detail' => 'As a bonus action, surround yourself with supernatural frost for 1 minute. You become lightly obscured, creatures more than 15 feet away have disadvantage on attacks against you, and enemies beginning their turn within 10 feet take cold damage equal to your proficiency bonus.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'conclave-of-the-forager' => [
            'label' => 'Conclave of the Forager',
            'gifts' => [
                [
                    'key' => 'foragers-remedies',
                    'label' => 'Forager\'s Remedies',
                    'level' => 3,
                    'summary' => 'Prepare herbal remedies after a long rest, including Mintleaf Draught, Basil Balm, Rosemary Tonic, Nettle Oil and Sagebrew.',
                    'detail' => 'After a long rest, prepare Forager\'s Remedies. Examples include Mintleaf Draught for temporary HP, Basil Balm to end poisoned, Rosemary Tonic for advantage on the next Wisdom saving throw, Nettle Oil for extra poison weapon damage, and Sagebrew for +1d4 on the drinker\'s next ability check. Higher levels increase preparation and potency.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'field-apothecary',
                    'label' => 'Field Apothecary',
                    'level' => 7,
                    'summary' => 'Administer one of your prepared remedies as a bonus action.',
                    'detail' => 'Administering one of your remedies becomes a bonus action.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'potent-harvest',
                    'label' => 'Potent Harvest',
                    'level' => 11,
                    'summary' => 'Damaging remedies ignore poison resistance and healing remedies restore extra HP equal to Wisdom modifier.',
                    'detail' => 'Your damaging remedies ignore poison resistance, while healing remedies restore additional HP equal to your Wisdom modifier.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'miracle-harvest',
                    'label' => 'Miracle Harvest',
                    'level' => 15,
                    'summary' => 'Once per long rest, create a legendary restorative mixture.',
                    'detail' => 'Once per long rest, create a legendary restorative mixture capable of restoring a creature to half its maximum HP and ending blinded, deafened, paralyzed or poisoned.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'spice-trail-hunter' => [
            'label' => 'Spice Trail Hunter',
            'gifts' => [
                [
                    'key' => 'spice-infusion',
                    'label' => 'Spice Infusion',
                    'level' => 3,
                    'summary' => 'Choose Chilli fire, Ginger thunder, Garlic radiant or Pepperleaf poison; once per turn an infused attack adds 1d6.',
                    'detail' => 'At 3rd level, choose a Spice Infusion when attacking: Chilli deals fire damage, Ginger thunder damage, Garlic radiant damage, or Pepperleaf poison damage. Once per turn an infused attack deals an additional 1d6 of the chosen type.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'smoke-step',
                    'label' => 'Smoke Step',
                    'level' => 7,
                    'summary' => 'After dealing elemental damage, move 10 feet without provoking opportunity attacks.',
                    'detail' => 'After dealing elemental damage, you may move 10 feet without provoking opportunity attacks.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'extra-hot',
                    'label' => 'Extra Hot',
                    'level' => 11,
                    'summary' => 'Spice Infusion rises to 2d6 and can change between attacks.',
                    'detail' => 'Spice Infusion increases to 2d6, and you may change infusion between attacks.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'the-final-seasoning',
                    'label' => 'The Final Seasoning',
                    'level' => 15,
                    'summary' => 'Once per long rest, empower one hit with every spice simultaneously.',
                    'detail' => 'Once per long rest, empower a hit with every spice simultaneously: +2d6 fire, +2d6 thunder, +2d6 radiant and +2d6 poison damage. Naturally, Sizzlarians consider this an entirely reasonable amount of seasoning.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'rindrunner' => [
            'label' => 'Rindrunner',
            'gifts' => [
                [
                    'key' => 'hardened-rind-and-cave-hunter',
                    'label' => 'Hardened Rind & Cave Hunter',
                    'level' => 3,
                    'summary' => 'Gain +1 AC in light or medium armour plus improved darkvision and underground tracking.',
                    'detail' => 'While wearing light or medium armour, gain +1 AC. Gain darkvision to 60 feet, or extend existing darkvision by 30 feet, and gain advantage on checks made to track creatures underground.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'wheyfinder',
                    'label' => 'Wheyfinder',
                    'level' => 7,
                    'summary' => 'Sense creatures moving through stone or earth within 10 feet.',
                    'detail' => 'You can sense creatures moving through stone or earth within 10 feet of you, preventing them from easily surprising you.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'piercing-wedge',
                    'label' => 'Piercing Wedge',
                    'level' => 11,
                    'summary' => 'Once per turn, ignore piercing or slashing resistance and add 1d8 damage.',
                    'detail' => 'Once per turn your weapon attack ignores resistance to piercing or slashing damage and deals an additional 1d8 damage.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'ancient-rind',
                    'label' => 'Ancient Rind',
                    'level' => 15,
                    'summary' => 'React to halve incoming damage a Wisdom-modifier number of times per long rest.',
                    'detail' => 'As a reaction when taking damage, halve that damage. You can do this a number of times equal to your Wisdom modifier per long rest.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'seedshot-conclave' => [
            'label' => 'Seedshot Conclave',
            'gifts' => [
                [
                    'key' => 'seedshots',
                    'label' => 'Seedshots',
                    'level' => 3,
                    'summary' => 'Learn magical seed ammunition including Vine, Burstmelon, Sunseed, Heavyseed and Bloom Seed.',
                    'detail' => 'Learn several Seedshots: Vine Seed restrains, Burstmelon Seed explodes for area damage, Sunseed produces brilliant light and can blind, Heavyseed knocks creatures backwards, and Bloom Seed creates temporary healing blossoms.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'ricochet-seed',
                    'label' => 'Ricochet Seed',
                    'level' => 7,
                    'summary' => 'Redirect a missed ranged projectile toward another creature within 15 feet.',
                    'detail' => 'When you miss a ranged attack, you can redirect the projectile toward another creature within 15 feet.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'double-germination',
                    'label' => 'Double Germination',
                    'level' => 11,
                    'summary' => 'A Seedshot can affect a second creature near its original target.',
                    'detail' => 'A Seedshot can affect a second creature near its original target.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'ancient-seed',
                    'label' => 'Ancient Seed',
                    'level' => 15,
                    'summary' => 'Once per long rest, grow a 30-foot-radius miniature enchanted forest for 1 minute.',
                    'detail' => 'Once per long rest, fire a legendary seed into the ground. A gigantic magical plant erupts, creating a 30-foot-radius miniature enchanted forest for 1 minute. Allies receive cover while enemies contend with grasping vines and difficult terrain.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'expiry-hunter' => [
            'label' => 'Expiry Hunter',
            'gifts' => [
                [
                    'key' => 'smell-the-spoiled-and-expiry-mark',
                    'label' => 'Smell the Spoiled & Expiry Mark',
                    'level' => 3,
                    'summary' => 'Sense undead, aberrations and decay within 60 feet; damage such creatures for an extra 1d8 radiant or necrotic once per turn.',
                    'detail' => 'You can sense undead, aberrations and creatures strongly affected by decay within 60 feet, although barriers can block the sense. Once per turn when you damage one of these creatures, deal an additional 1d8 radiant or necrotic damage.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'past-the-date',
                    'label' => 'Past the Date',
                    'level' => 7,
                    'summary' => 'Gain poison resistance and advantage on saves against poison and disease.',
                    'detail' => 'Gain resistance to poison damage and advantage on saving throws against poison and disease.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'put-it-back',
                    'label' => 'Put It Back',
                    'level' => 11,
                    'summary' => 'React to stop an undead or corrupted creature within 30 feet from regaining HP.',
                    'detail' => 'When an undead or corrupted creature within 30 feet regains HP, use your reaction to reduce the healing to 0. You can use this a number of times equal to your proficiency bonus per long rest.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'final-recall',
                    'label' => 'Final Recall',
                    'level' => 15,
                    'summary' => 'Defeated undead, aberrations and corrupted creatures cannot self-return, and a kill can transfer your mark.',
                    'detail' => 'When you reduce an undead, aberration or corrupted creature to 0 HP, it cannot regenerate or return to life through one of its own traits. Once per turn, defeating such a creature also lets you immediately mark another creature within 60 feet.',
                    'mode' => 'automatic',
                ],
            ],
        ],
    ];

    private function __construct(
        private string $path
    ) {
        if (! isset(self::PATHS[$path])) {
            throw new InvalidArgumentException(
                'Unknown Ranger Path gift progression.'
            );
        }
    }

    public static function for(
        string $path
    ): self {
        return new self(sanitize_key($path));
    }

    /** @return array<int,self> */
    public static function allDefinitions(): array
    {
        return array_map(
            static fn (string $path): self => self::for($path),
            array_keys(self::PATHS)
        );
    }

    public function supports(
        string $pathKey
    ): bool {
        return sanitize_key($pathKey) === $this->path;
    }

    public function pathKey(): string
    {
        return $this->path;
    }

    public function pathLabel(): string
    {
        return self::PATHS[$this->path]['label'];
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return self::PATHS[$this->path]['gifts'];
    }
}
