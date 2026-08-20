<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Canon Great Marketrealm Bard College gifts from III.12.12B.
 *
 * Mechanics are derived from The Great Marketrealm - Players Handbook.
 * The handbook intentionally gives College of Nostalgia no Level 14 feature;
 * this catalogue preserves that source rather than inventing a capstone.
 */
final class BardCollegeGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /** @var array<string,array{label:string,gifts:array<int,array<string,mixed>>}> */
    private const COLLEGES = [
        'college-of-the-seasoned-song' => [
            'label' => 'College of the Seasoned Song',
            'gifts' => [
                [
                    'key' => 'spice-notes',
                    'label' => 'Spice Notes',
                    'level' => 3,
                    'summary' => 'Spend Bardic Inspiration on Sweet, Bitter or Sour Notes that heal, hinder a save or reduce speed.',
                    'detail' => 'Sweet Note adds 1d4 HP to an ally alongside the normal Bardic Inspiration effect. Bitter Note makes an enemy subtract 1d4 from its next saving throw. Sour Note disorients a creature and reduces its speed by 10 feet for 1 round.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'herbal-harmonization',
                    'label' => 'Herbal Harmonization',
                    'level' => 3,
                    'summary' => 'Gain Herbalism Kit and Arcana proficiency, and twice per long rest lace enchantment or illusion magic with herbal distraction.',
                    'detail' => 'Gain proficiency with the Herbalism Kit and Arcana. When casting an enchantment or illusion spell, flavour it with herbal effects such as calming fog or fragrant vines; the target has disadvantage on its initial saving throw. Use this benefit twice per long rest.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'choral-infusion',
                    'label' => 'Choral Infusion',
                    'level' => 6,
                    'summary' => 'Infuse a short-rest meal for up to six creatures with Wisdom-save advantage, temporary HP or poison resistance.',
                    'detail' => 'During a short rest, infuse food or drink through song. Up to six creatures consuming the meal gain one chosen benefit: advantage on Wisdom saving throws, temporary HP equal to your Charisma modifier + Bard level, or resistance to poison for 1 hour.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'symphony-of-the-senses',
                    'label' => 'Symphony of the Senses',
                    'level' => 14,
                    'summary' => 'Perform a one-minute masterpiece that can incapacitate nearby enemies with rapture.',
                    'detail' => 'As an action, begin a Masterpiece Performance lasting up to 1 minute. Enemies within 30 feet make a Wisdom saving throw or become incapacitated with rapture until they take damage.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'college-of-nostalgia' => [
            'label' => 'College of Nostalgia',
            'gifts' => [
                [
                    'key' => 'jingle-strike',
                    'label' => 'Jingle Strike',
                    'level' => 3,
                    'summary' => 'Spend Bardic Inspiration after dealing damage or casting a spell to charm, hinder a save, or add psychic fear.',
                    'detail' => 'After dealing damage or casting a spell on a creature, expend Bardic Inspiration and choose a jingle. Misty Memories charms the target until the end of its next turn. Retro Regret gives disadvantage on its next saving throw. Flashback Fury adds 1d6 psychic damage and frightens the target on a failed Wisdom save.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'viral-catchphrase',
                    'label' => 'Viral Catchphrase',
                    'level' => 3,
                    'summary' => 'Spend two Inspiration dice to inspire every ally within 10 feet who can hear your catchphrase.',
                    'detail' => 'As a bonus action, deliver your catchphrase to allies within 10 feet who can hear you. Spending two Bardic Inspiration dice grants Bardic Inspiration to all of those allies.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'forgotten-favorite',
                    'label' => 'Forgotten Favorite',
                    'level' => 6,
                    'summary' => 'Once per long rest, a high Inspiration roll can leave you at 1 HP and briefly invisible instead of falling to 0 HP.',
                    'detail' => 'Once per long rest when reduced to 0 HP, roll a Bardic Inspiration die. On a result of 5 or higher, remain at 1 HP instead and become invisible until the start of your next turn.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'college-of-preservation' => [
            'label' => 'College of Preservation',
            'gifts' => [
                [
                    'key' => 'canning-chant',
                    'label' => 'Canning Chant',
                    'level' => 3,
                    'summary' => 'Cast Gentle Repose without material components and ritualize Lesser Restoration through preservation once per long rest.',
                    'detail' => 'You can cast Gentle Repose without material components. Once per long rest, you can cast Lesser Restoration as a ritual by pickling or sealing the subject of the magic.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'preserved-performance',
                    'label' => 'Preserved Performance',
                    'level' => 3,
                    'summary' => 'Bardic Inspiration also grants +2 temporary HP and poison resistance for 1 minute.',
                    'detail' => 'A creature receiving your Bardic Inspiration also gains 2 temporary hit points and resistance to poison for 1 minute.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'pickled-panic',
                    'label' => 'Pickled Panic',
                    'level' => 6,
                    'summary' => 'Once per short rest, fill a 15-foot cube with sour brine that can poison and blind creatures for a turn.',
                    'detail' => 'Once per short rest, unleash a sour-brined cloud in a 15-foot cube. Creatures caught in it make a Constitution saving throw or become poisoned and blinded for 1 turn.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'timeless-encore',
                    'label' => 'Timeless Encore',
                    'level' => 14,
                    'summary' => 'Once per long rest, reproduce a Bard spell that was cast within the previous hour.',
                    'detail' => 'Once per long rest, replicate the effect of any Bard spell cast within the last hour, as though its magic had been recorded and preserved in a jar.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'charcutaire' => [
            'label' => 'Charcutaire',
            'gifts' => [
                [
                    'key' => 'cured-insight',
                    'label' => 'Cured Insight',
                    'level' => 3,
                    'summary' => 'Magically discern a creature’s desires by observing how it eats.',
                    'detail' => 'By observing how a creature eats, you can magically discern its desires, turning culinary observation into social and psychic insight.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'meatplatter-performance',
                    'label' => 'Meatplatter Performance',
                    'level' => 3,
                    'summary' => 'Your Bardic Inspiration grants temporary HP alongside its normal bonus.',
                    'detail' => 'When you grant Bardic Inspiration, the recipient also gains temporary hit points in addition to the normal Inspiration benefit.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'flavour-pairing',
                    'label' => 'Flavour Pairing',
                    'level' => 6,
                    'summary' => 'A chosen ally can immediately move or attack as a reaction when you support them with a spell.',
                    'detail' => 'Choose a party member as your pairing. When you cast a support spell on that ally, they can immediately use their reaction to move or make an attack.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'cold-cut-wave',
                    'label' => 'Cold Cut Wave',
                    'level' => 14,
                    'summary' => 'Release an area blast of razor-thin meats dealing psychic and slashing damage against a Dexterity save.',
                    'detail' => 'Release a slicing wave of razor-thin meats across an area. Creatures caught in the blast make a Dexterity saving throw against combined psychic and slashing damage.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'college-of-culinary-crescendo' => [
            'label' => 'College of Culinary Crescendo',
            'gifts' => [
                [
                    'key' => 'sizzling-solo',
                    'label' => 'Sizzling Solo',
                    'level' => 3,
                    'summary' => 'Replace normal Bardic Inspiration with a Flash-Fry Flourish, Saucy Sidestep or Seasoned Timing Culinary Crescendo.',
                    'detail' => 'When using Bardic Inspiration, choose a Culinary Crescendo instead of its normal effect. Flash-Fry Flourish adds the die to a melee attack and deals the same die as bonus fire damage on a hit. Saucy Sidestep adds the die to a saving throw and, on a total of 20 or more, permits half-speed movement without opportunity attacks. Seasoned Timing adds the die to an ability check and grants temporary HP equal to the die + your Charisma modifier when the check succeeds.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'cooks-toolkit',
                    'label' => 'Cook’s Toolkit',
                    'level' => 3,
                    'summary' => 'Gain cook’s utensils proficiency, use them as a Bard focus, and prepare a short-rest meal that wards one Constitution or Wisdom save.',
                    'detail' => 'Gain proficiency with cook’s utensils and use them as a spellcasting focus for Bard spells. During a short rest, cook a magical meal for up to six creatures; each eater gains advantage on one Constitution or Wisdom saving throw made before their next long rest.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'boiling-over',
                    'label' => 'Boiling Over',
                    'level' => 6,
                    'summary' => 'Once per turn, add Charisma modifier fire or acid damage while cooking-equipped, and cast Heat Metal free once per long rest.',
                    'detail' => 'Once per turn when a Bard spell or weapon attack deals damage, deal additional fire or acid damage equal to your Charisma modifier while holding cook’s utensils or a cooking-themed instrument. Once per long rest, cast Heat Metal without expending a spell slot or components.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'kitchen-orchestra',
                    'label' => 'Kitchen Orchestra',
                    'level' => 14,
                    'summary' => 'Conduct a one-minute kitchen symphony that can burn enemies, slow them with cold, or heal and empower allies each round.',
                    'detail' => 'As an action, begin a concentration performance for up to 1 minute. Each round as a bonus action, choose Flambé Frenzy (enemies in 30 feet make a Dexterity save or take 4d6 fire damage), Chill Flash (enemies make a Constitution save or are slowed until the end of their next turn), or Buffet Boost (allies regain 1d8 HP and their next attack deals +1d6 damage). Regain this feature after a long rest.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'college-of-confection' => [
            'label' => 'College of Confection',
            'gifts' => [
                [
                    'key' => 'sugar-sonata',
                    'label' => 'Sugar Sonata',
                    'level' => 3,
                    'summary' => 'Bonus-action charm a creature that can taste or smell until the end of your next turn on a failed save.',
                    'detail' => 'As a bonus action, target a creature able to taste or smell. It makes a saving throw against your spell save DC or becomes charmed until the end of your next turn.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'lickable-lullaby',
                    'label' => 'Lickable Lullaby',
                    'level' => 6,
                    'summary' => 'Bardic Inspiration also grants temporary HP equal to Charisma modifier + Bard level.',
                    'detail' => 'Whenever you grant Bardic Inspiration, the target also gains temporary hit points equal to your Charisma modifier + your Bard level.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'candy-clone',
                    'label' => 'Candy Clone',
                    'level' => 14,
                    'summary' => 'Create a nearby one-minute taffy duplicate that can absorb one attack or spell aimed at you.',
                    'detail' => 'As an action, create a taffy dummy of yourself in an unoccupied space within 30 feet. It lasts 1 minute and can absorb one attack or spell that targets you.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'college-of-churned-verse' => [
            'label' => 'College of Churned Verse',
            'gifts' => [
                [
                    'key' => 'creamtone-cantrips',
                    'label' => 'Creamtone Cantrips',
                    'level' => 3,
                    'summary' => 'Learn Frostbite and Prestidigitation as Bard spells without counting them against your cantrips known.',
                    'detail' => 'Learn Frostbite and Prestidigitation. They count as Bard spells for you and do not count against the number of cantrips you know.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'harmonic-churn',
                    'label' => 'Harmonic Churn',
                    'level' => 3,
                    'summary' => 'Bardic Inspiration grants brief fire resistance and heat endurance, with cold retaliation against melee attackers.',
                    'detail' => 'When you grant Bardic Inspiration, the recipient gains resistance to fire damage and ignores exhaustion from extreme heat for 1 minute. If a melee attack hits them during that time, the attacker takes cold damage equal to your Charisma modifier.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'chill-out',
                    'label' => 'Chill Out',
                    'level' => 6,
                    'summary' => 'React when a nearby creature fails a Wisdom or Charisma save to slow it for 1 round, proficiency-bonus times per long rest.',
                    'detail' => 'When a creature within 60 feet fails a Wisdom or Charisma saving throw, use your reaction to freeze its mind with lyrical frost and slow it for 1 round. Use this feature a number of times equal to your proficiency bonus per long rest.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'flavourful-refrain',
                    'label' => 'Flavourful Refrain',
                    'level' => 14,
                    'summary' => 'Perform a Frozen Refrain that grants up to six allies temporary HP plus fire and psychic resistance.',
                    'detail' => 'Perform a one-minute Frozen Refrain. Up to six allies gain temporary hit points equal to your Bard level + Charisma modifier and resistance to fire and psychic damage for 10 minutes.',
                    'mode' => 'automatic',
                ],
            ],
        ],
    ];

    private function __construct(
        private string $path
    ) {
        if (! isset(self::COLLEGES[$path])) {
            throw new InvalidArgumentException(
                'Unknown Bard College gift progression.'
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
            array_keys(self::COLLEGES)
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
        return self::COLLEGES[$this->path]['label'];
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return self::COLLEGES[$this->path]['gifts'];
    }
}
