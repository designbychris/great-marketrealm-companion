<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Canon Great Marketrealm Artificer Specialisation gifts from III.12.13B.
 *
 * Mechanics are derived from The Great Marketrealm - Players Handbook.
 * The Sous-Sorcerer is intentionally preserved as an uneven progression:
 * the handbook supplies an unlevelled core package and Flavour Surge, but
 * does not supply later Level 5, 9 or 15 features.
 */
final class ArtificerSpecialisationGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /** @var array<string,array{label:string,gifts:array<int,array<string,mixed>>}> */
    private const SPECIALISATIONS = [
        'the-spice-engineer' => [
            'label' => 'The Spice Engineer',
            'gifts' => [
                [
                    'key' => 'spicecrafting',
                    'label' => 'Spicecrafting',
                    'level' => 3,
                    'summary' => 'Blend one magical spice effect into a spell or Artificer infusion, with uses tied to Intelligence.',
                    'detail' => 'Gain cook’s-utensil proficiency if needed and a spice kit with four effects. Smoked Spice adds 1d4 fire damage; Cool Herb Blend can halve a target’s speed after a Constitution save; Sour Salt gives disadvantage on the target’s next Wisdom save; Umami Bomb adds 1d6 healing to your magic. Use Spicecrafting a number of times equal to your Intelligence modifier per long rest.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'infused-condiments',
                    'label' => 'Infused Condiments',
                    'level' => 3,
                    'summary' => 'Prepare proficiency-bonus Condiment Cannisters after a long rest as offensive, healing or weapon-enhancing consumables.',
                    'detail' => 'After a long rest, create Condiment Cannisters equal to your proficiency bonus. Pepper Popper is thrown 30 feet and bursts in a 10-foot radius for 2d6 fire damage on a failed Dexterity save, also blinding until the start of your next turn. Healing Broth restores 2d4 + Intelligence modifier HP as a bonus action. Tangy Glaze gives a weapon +1 to attack and damage rolls for 1 minute. Unused cannisters expire at your next long rest.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'flavour-cascade',
                    'label' => 'Flavour Cascade',
                    'level' => 5,
                    'summary' => 'Spend a spell slot after spell or infused-weapon damage to splash extra spice-linked damage across nearby foes.',
                    'detail' => 'When a spell or infused weapon deals damage, expend a spell slot to trigger a Flavour Cascade. The target and up to two nearby enemies take an additional 2d6 damage of a spice-associated type such as fire, cold or psychic, and must make a Constitution save or suffer disadvantage on their next attack roll.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'gourmet-arsenal',
                    'label' => 'Gourmet Arsenal',
                    'level' => 9,
                    'summary' => 'Apply condiments to weapons as a bonus action and add Ghost Chili Grenade and Basil Balm to your cannister recipes.',
                    'detail' => 'Infused Condiments can now be applied to weapons as a bonus action. Ghost Chili Grenade fills a 15-foot cone; creatures failing a Constitution save take 4d6 fire damage and become frightened until the end of their next turn. Basil Balm restores 3d8 HP and removes one of blinded, deafened, frightened or poisoned.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'the-grand-seasoning',
                    'label' => 'The Grand Seasoning',
                    'level' => 15,
                    'summary' => 'Once per long rest, season a 30-foot battlefield with mass healing, defensive advantage, elemental weapon damage and a stunning enemy salt.',
                    'detail' => 'As an action once per long rest, unleash The Grand Seasoning. Allies within 30 feet regain 4d8 HP, gain advantage on saving throws against conditions and add 1d6 chosen fire, cold, lightning or acid damage to weapon attacks for 1 minute. Enemies in the area make a Constitution save or become salted and stunned until the end of your next turn.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'the-cheesemonger' => [
            'label' => 'The Cheesemonger',
            'gifts' => [
                [
                    'key' => 'cheesy-constructs',
                    'label' => 'Cheesy Constructs',
                    'level' => 3,
                    'summary' => 'Animate a Cheeseling construct, expanding to two simultaneous Cheeselings at Level 9.',
                    'detail' => 'Animate one Tiny Cheeseling construct with AC 13, 15 HP and an attack dealing 1d6 bludgeoning plus 1d6 acid damage. It counts as a Steel Defender for infusions. At Level 9, you can control two Cheeselings at once.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'cheese-forged-infusions',
                    'label' => 'Cheese-Forged Infusions',
                    'level' => 3,
                    'summary' => 'Create signature Gouda Grenades, Brie Shields and psychic-resistant Rindplate.',
                    'detail' => 'Your dairycraft supports special magical items. Gouda Grenade deals 1d10 thunder damage with a 10-foot splash. Brie Shield can grant +2 AC as a reaction once per rest. Rindplate is light armour that grants resistance to psychic damage.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'dairy-density',
                    'label' => 'Dairy Density',
                    'level' => 5,
                    'summary' => 'Choose Hardened Rind for +1 AC or Melty Core to reduce incoming damage and become difficult to grapple.',
                    'detail' => 'Choose one dairy adaptation. Hardened Rind grants +1 AC. Melty Core reduces damage when you are hit by your Intelligence modifier and makes you slippery, imposing disadvantage on attempts to grapple you.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'cheese-overload',
                    'label' => 'Cheese Overload',
                    'level' => 9,
                    'summary' => 'Once per long rest, overload cheese-infused gear in a 15-foot burst for heavy force damage and a possible stun.',
                    'detail' => 'Once per long rest, overload all cheese-infused items in a 15-foot burst. Enemies take 5d8 force damage and make a Constitution save or become stunned.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'grand-gruyere',
                    'label' => 'Grand Gruyère',
                    'level' => 15,
                    'summary' => 'Cheeselings explode in acid when destroyed, and you can create a temporary Large multiattacking version.',
                    'detail' => 'Your Cheeselings explode on death for 2d6 acid damage. You can also create a Large Cheeseling that lasts 10 minutes and gains Multiattack.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'the-sous-sorcerer' => [
            'label' => 'The Sous-Sorcerer',
            'gifts' => [
                [
                    'key' => 'sous-sorcerer-core-features',
                    'label' => 'Sous-Sorcerer Core Features',
                    'level' => 3,
                    'summary' => 'Use an Arcane Cook’s Tool as focus and spell store, substitute culinary tools for components, and package infusions as throwable flavour bombs.',
                    'detail' => 'At the Level 3 Specialisation boundary, the handbook supplies the Sous-Sorcerer’s unlevelled core package. Gain an Arcane Cook’s Tool that serves as an arcane focus and stores one spell of up to 2nd level. Culinary tools can substitute for material components, and infusions may be stored in jars or utensils as throwable flavour bombs carrying spell effects.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'flavour-surge',
                    'label' => 'Flavour Surge',
                    'level' => 3,
                    'summary' => 'Once per short rest, maximize one spell damage die and one infusion die in the same turn.',
                    'detail' => 'Once per short rest, maximize one damage die from a spell and one infusion die during the same turn.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'the-culinary-engineer' => [
            'label' => 'The Culinary Engineer',
            'gifts' => [
                [
                    'key' => 'tools-of-the-trade',
                    'label' => 'Tools of the Trade',
                    'level' => 3,
                    'summary' => 'Gain cook’s-utensil and butcher’s-tool proficiency and use cook’s utensils as your spellcasting focus.',
                    'detail' => 'Gain proficiency with cook’s utensils and butcher’s tools if you do not already have them. You can use cook’s utensils as your spellcasting focus.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'culinary-infusions',
                    'label' => 'Culinary Infusions',
                    'level' => 3,
                    'summary' => 'Add four magical kitchen-tool infusions: protective pan, sharp grater, telekinetic tongs and retaliatory rolling pin.',
                    'detail' => 'Gain special culinary infusion options in addition to normal Artificer choices. Pan of Sizzling Protection grants +1 AC and fire resistance. Grater of Sharpness grants +1 to attack and damage and casts Grease once per day. Tongs of Telekinesis provide Mage Hand at will plus a shove or pull once per short rest. Rolling Pin of Reprisal is a club dealing an extra 1d6 damage if its wielder was damaged last turn.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'battle-feast',
                    'label' => 'Battle Feast',
                    'level' => 5,
                    'summary' => 'Prepare Intelligence-modifier quick magical meals per long rest for healing, fiery offense or concentration support.',
                    'detail' => 'As an action, prepare one Quick Magical Meal, usable Intelligence-modifier times per long rest. Hearty Skillet Hash heals 3d6 + Intelligence modifier HP and removes charmed, frightened or poisoned. Spicy Surprise forces a Dexterity save against 4d6 fire damage and disadvantage on the target’s next attack. Mystic Mousse grants advantage on concentration saves and +10 feet speed for 1 minute. A prepared meal loses potency if unused after 1 minute.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'animated-utensils',
                    'label' => 'Animated Utensils',
                    'level' => 9,
                    'summary' => 'Animate a swarm of enchanted kitchen tools for 1 minute, refreshing with a long rest or 2nd-level spell slot.',
                    'detail' => 'As an action, animate 1d4 + Intelligence modifier enchanted utensils, pans or tools as Tiny or Small Flying Swords or Animated Objects acting on your initiative. They last 1 minute or until reduced to 0 HP. Use once per long rest, or again by expending a 2nd-level spell slot. Mending also restores 1d6 HP to an animated cooking tool.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'master-of-magical-cuisine',
                    'label' => 'Master of Magical Cuisine',
                    'level' => 15,
                    'summary' => 'Prepare a six-person Legendary Meal granting temporary HP, resistance and Constitution-save advantage, plus culinary infusion capacity.',
                    'detail' => 'Prepare a Legendary Meal as a 10-minute ritual, or in 1 minute with tools, for up to six creatures. Eaters gain 20 temporary HP, resistance to one chosen damage type and advantage on Constitution saves for 1 hour. Once per day, one eater may automatically succeed on a death save. You can also maintain two additional infused items when they are culinary tools, tableware or cookware.',
                    'mode' => 'automatic',
                ],
            ],
        ],
    ];

    private function __construct(
        private string $path
    ) {
        if (! isset(self::SPECIALISATIONS[$path])) {
            throw new InvalidArgumentException(
                'Unknown Artificer Specialisation gift progression.'
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
            array_keys(self::SPECIALISATIONS)
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
        return self::SPECIALISATIONS[$this->path]['label'];
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return self::SPECIALISATIONS[$this->path]['gifts'];
    }
}
