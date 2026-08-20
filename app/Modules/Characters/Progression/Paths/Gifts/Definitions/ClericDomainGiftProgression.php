<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Great Marketrealm Cleric Domain gifts.
 *
 * III.12.11B preserves supplied features and lightly normalizes older,
 * incomplete Domains onto the Cleric's 1 / 2 / 6 / 8 / 17 cadence.
 */
final class ClericDomainGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /** @var array<string,array{label:string,gifts:array<int,array<string,mixed>>}> */
    private const DOMAINS = [
        'domain-of-sweetness' => [
            'label' => 'Domain of Sweetness',
            'gifts' => [
                [
                    'key' => 'bonus-cantrips-and-sweet-sanctuary',
                    'label' => 'Bonus Cantrips & Sweet Sanctuary',
                    'level' => 1,
                    'summary' => 'Learn Prestidigitation and Druidcraft; healing an ally also grants temporary HP equal to Cleric level + Wisdom modifier for 1 minute.',
                    'detail' => 'Learn Prestidigitation and Druidcraft. Prestidigitation may create candy-themed sensory effects. When you cast a healing spell on an ally, they also gain temporary hit points equal to your Cleric level + Wisdom modifier for 1 minute or until lost.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'sugarburst',
                    'label' => 'Channel Divinity: Sugarburst',
                    'level' => 2,
                    'summary' => 'Emit a 15-foot radiant sugar burst: hostile creatures risk blindness while allies gain 1d6 temporary HP and brief fear-save advantage.',
                    'detail' => 'As an action, expend Channel Divinity to emit a 15-foot burst of radiant sugar-light. Hostile creatures make a Dexterity saving throw or become blinded and covered in sparkling sticky residue for 1 minute, repeating the save at the end of each turn. Allies in range gain 1d6 temporary hit points and advantage on saving throws against fear until the end of their next turn.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'sticky-ward',
                    'label' => 'Sticky Ward',
                    'level' => 6,
                    'summary' => 'Healing or buffing an ally can partially restrain a hostile creature within 5 feet of them on a failed Strength save.',
                    'detail' => 'Whenever you cast a spell that targets an ally and heals or buffs them, choose a hostile creature within 5 feet of that ally. It must succeed on a Strength saving throw or have its speed halved and disadvantage on Dexterity checks until the start of your next turn.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'sticky-smite',
                    'label' => 'Divine Strike: Sticky Smite',
                    'level' => 8,
                    'summary' => 'Once per turn, weapon hits deal +1d8 radiant damage, increasing to 2d8 at Cleric 14; undead suffer an additional targeting penalty.',
                    'detail' => 'Once on each of your turns when you hit with a weapon attack, deal an extra 1d8 radiant damage. At Cleric level 14, this becomes 2d8. If the target is undead, it also has disadvantage on attack rolls against creatures other than you until the start of your next turn.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'ascension-of-the-sugarcloud',
                    'label' => 'Ascension of the Sugarcloud',
                    'level' => 17,
                    'summary' => 'Once per long rest, or by expending a 5th-level slot, become an ethereal cotton-candy form for 1 minute.',
                    'detail' => 'As a bonus action, once per long rest or by expending a 5th-level spell slot, become an ethereal cotton-candy form for 1 minute. Gain a 60-foot flying speed and hover, resistance to all damage except force and psychic, maximum healing from your healing spells, and impose disadvantage on Wisdom saving throws for hostile creatures within 10 feet.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'domain-of-the-golden-arches' => [
            'label' => 'Domain of the Golden Arches',
            'gifts' => [
                [
                    'key' => 'divine-combo-meal-and-sacred-sauce',
                    'label' => 'Divine Combo Meal & Sacred Sauce',
                    'level' => 1,
                    'summary' => 'Healing spells briefly grant +2 AC, while Grease and Create Food and Water become signature sacred fare.',
                    'detail' => 'When you cast a healing spell on an ally, that ally gains +2 AC until the start of your next turn. Grease and Create Food and Water are treated as signature Domain spells, flavoured as sacred sauce and heavenly fries.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'order-up',
                    'label' => 'Channel Divinity: Order Up',
                    'level' => 2,
                    'summary' => 'Expend Channel Divinity to teleport to an ally within 30 feet and immediately deliver a touch-range spell.',
                    'detail' => 'As an action, expend Channel Divinity to teleport to an ally you can see within 30 feet. As part of the same action, you may cast a touch-range spell on that ally, expending the spell slot normally.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'express-blessing',
                    'label' => 'Express Blessing',
                    'level' => 6,
                    'summary' => 'Once per turn, an ally you heal or buff may move 10 feet without provoking opportunity attacks.',
                    'detail' => 'Once per turn when you cast a spell that heals or buffs an ally, that ally may immediately move up to 10 feet without provoking opportunity attacks. This editorial milestone completes the older Domain progression while preserving its rapid-service identity.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
                [
                    'key' => 'golden-fry-strike',
                    'label' => 'Divine Strike: Golden Fry',
                    'level' => 8,
                    'summary' => 'Once per turn, a weapon hit deals +1d8 radiant damage, increasing to 2d8 at Cleric 14.',
                    'detail' => 'Once on each of your turns when you hit with a weapon attack, deal an extra 1d8 radiant damage. At Cleric level 14, this becomes 2d8. This editorial milestone gives the older Domain a complete Level 8 Cleric feature.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
                [
                    'key' => 'happy-heal-hour',
                    'label' => 'Happy Heal Hour',
                    'level' => 17,
                    'summary' => 'Once per long rest, begin a 1-minute sacred service rush in which your healing spells restore their maximum possible hit points.',
                    'detail' => 'As a bonus action once per long rest, begin Happy Heal Hour for 1 minute. During it, any spell you cast that restores hit points restores the maximum possible amount instead of rolling. This clarifies the supplied “all healing spells heal for max value once per long rest” wording into a usable capstone window.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
            ],
        ],

        'domain-of-dairy' => [
            'label' => 'Domain of Dairy',
            'gifts' => [
                [
                    'key' => 'dairy-domain-magic',
                    'label' => 'Dairy Domain Magic',
                    'level' => 1,
                    'summary' => 'Grease is a signature Domain spell and the Dairy Domain spell table is always available at its listed Cleric levels.',
                    'detail' => 'The original “Bonus Cantrip: Grease” wording is corrected: Grease is a 1st-level spell, not a cantrip. It remains a signature Domain spell and the supplied Dairy Domain spell table is preserved.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
                [
                    'key' => 'curdled-blessing',
                    'label' => 'Channel Divinity: Curdled Blessing',
                    'level' => 2,
                    'summary' => 'Create a 10-foot sticky aura for 1 minute: allies gain +1 AC and enemies treat the area as difficult terrain.',
                    'detail' => 'Expend Channel Divinity as an action to create a 10-foot-radius sticky aura around you for 1 minute. Allies in the aura gain +1 AC and enemies treat the area as difficult terrain. The supplied feature is normalized to Level 2 because it explicitly spends Channel Divinity.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
                [
                    'key' => 'stinky-salvation',
                    'label' => 'Stinky Salvation',
                    'level' => 6,
                    'summary' => 'Once per long rest, you or an ally within 30 feet may reroll a failed saving throw against poison or disease.',
                    'detail' => 'When you or an ally within 30 feet fails a saving throw against poison or disease, allow that creature to reroll the saving throw. This feature can be used once per long rest.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'cultured-smite',
                    'label' => 'Divine Strike: Cultured Smite',
                    'level' => 8,
                    'summary' => 'Once per turn, a weapon hit deals +1d8 radiant or cold damage, increasing to 2d8 at Cleric 14.',
                    'detail' => 'Once on each of your turns when you hit with a weapon attack, deal an extra 1d8 radiant or cold damage. At Cleric level 14, this becomes 2d8. This editorial feature fills the older Domain’s missing Level 8 milestone.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
                [
                    'key' => 'holy-butterstorm',
                    'label' => 'Holy Butterstorm',
                    'level' => 17,
                    'summary' => 'Once per long rest, call down burning clarified butter in a 30-foot radius for 6d8 radiant + 2d8 fire damage and a possible knockdown.',
                    'detail' => 'Once per long rest, call down a golden rain in a 30-foot radius. Enemies make a Dexterity saving throw; on a failed save they fall prone and take 6d8 radiant plus 2d8 fire damage as burning clarified butter.',
                    'mode' => 'automatic',
                ],
            ],
        ],

        'domain-of-seasoning' => [
            'label' => 'Domain of Seasoning',
            'gifts' => [
                [
                    'key' => 'flavourful-touch',
                    'label' => 'Flavourful Touch',
                    'level' => 1,
                    'summary' => 'Flavour or ruin food and water at will; once per long rest Zest a weapon for +1 to hit and damage for 1 hour.',
                    'detail' => 'As an action, flavour or ruin food or water. Once per long rest, Zest a weapon for 1 hour, granting +1 to attack and damage rolls with that weapon.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'salt-the-earth',
                    'label' => 'Channel Divinity: Salt the Earth',
                    'level' => 2,
                    'summary' => 'Consecrate a 15-foot-radius area for 1 minute, suppressing enemy healing, regeneration and potion benefits.',
                    'detail' => 'Expend Channel Divinity to consecrate a 15-foot-radius area for 1 minute. Enemies in the area have disadvantage on healing and regeneration effects and gain no benefit from potions.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'searing-seasoning',
                    'label' => 'Searing Seasoning',
                    'level' => 6,
                    'summary' => 'When a creature fails a save against one of your fire, poison or acid spells, it takes an extra 1d8 damage of that type.',
                    'detail' => 'When a creature fails a saving throw against a fire, poison, or acid spell you cast, it takes an extra 1d8 damage of that spell’s damage type.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'seasoned-divine-strike',
                    'label' => 'Divine Strike: Seasoned Edge',
                    'level' => 8,
                    'summary' => 'Once per turn, a weapon hit deals +1d8 fire or poison damage, increasing to 2d8 at Cleric 14.',
                    'detail' => 'Once on each of your turns when you hit with a weapon attack, deal an extra 1d8 fire or poison damage. At Cleric level 14, this becomes 2d8.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'perfect-balance',
                    'label' => 'Perfect Balance',
                    'level' => 17,
                    'summary' => 'Once per long rest for 1 minute, your fire, poison and acid damage ignores resistance and your Seasoning effects cannot be suppressed by mundane food or drink.',
                    'detail' => 'As a bonus action once per long rest, embody perfect seasoning for 1 minute. Fire, poison and acid damage from your Cleric spells ignores resistance. Immunity is not bypassed. This restrained editorial capstone completes the older Domain without changing its balance-and-flavour identity.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
            ],
        ],

        'domain-of-cultivation' => [
            'label' => 'Domain of Cultivation',
            'gifts' => [
                [
                    'key' => 'cultivator-proficiencies',
                    'label' => 'Cultivator Proficiencies',
                    'level' => 1,
                    'summary' => 'Gain proficiency in Nature and brewer’s supplies.',
                    'detail' => 'Gain proficiency in Nature and brewer’s supplies, reflecting patient knowledge of culture, aging and sacred preparation.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'blessed-brine',
                    'label' => 'Channel Divinity: Blessed Brine',
                    'level' => 2,
                    'summary' => 'Create a 15-foot briny aura for 1 minute: undead suffer disadvantaged saves and allies regain 1d6 HP at the start of their turns.',
                    'detail' => 'Expend Channel Divinity as an action to conjure a 15-foot-radius briny aura for 1 minute. Undead in the aura have disadvantage on saving throws. Allies in the aura regain 1d6 hit points at the start of their turns.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'patient-culture',
                    'label' => 'Patient Culture',
                    'level' => 6,
                    'summary' => 'Allies healed by your Cleric spells gain advantage on their next save against poison or disease before the end of their next turn.',
                    'detail' => 'When a creature regains hit points from one of your Cleric spells, it gains advantage on the next saving throw it makes against poison or disease before the end of its next turn. This editorial milestone develops the Domain’s patience-and-preservation identity.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
                [
                    'key' => 'cultivated-potency',
                    'label' => 'Potent Spellcasting: Cultivated Faith',
                    'level' => 8,
                    'summary' => 'Add your Wisdom modifier to the damage you deal with any Cleric cantrip.',
                    'detail' => 'Add your Wisdom modifier to the damage you deal with any Cleric cantrip. This editorial Level 8 feature gives the support-focused Domain a standard Cleric offensive progression without introducing a new weapon identity.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
                [
                    'key' => 'sacred-vintage',
                    'label' => 'Sacred Vintage',
                    'level' => 17,
                    'summary' => 'Once per long rest, create a 30-foot aura for 1 minute that strengthens restoration and protects allies from poison and disease.',
                    'detail' => 'As an action once per long rest, create a 30-foot aura for 1 minute. Allies in the aura have advantage on saving throws against poison and disease, and whenever one of your Cleric spells restores hit points to a creature in the aura, add your Wisdom modifier to one healing roll. This editorial capstone emphasizes aging, patience and mature restorative faith.',
                    'mode' => 'automatic',
                    'editorial' => true,
                ],
            ],
        ],

        'domain-of-fermentation' => [
            'label' => 'Domain of Fermentation',
            'gifts' => [
                [
                    'key' => 'ferment-touch-and-proficiencies',
                    'label' => 'Ferment Touch & Bonus Proficiencies',
                    'level' => 1,
                    'summary' => 'Gain Nature and brewer’s supplies; Ferment Touch heals allies, preserves corpses or acids enemies, scaling with Cleric level.',
                    'detail' => 'Gain proficiency with brewer’s supplies and Nature. As an action, use Ferment Touch on a creature or substance within 5 feet. An ally regains 1d8 + Wisdom modifier HP, once per creature per long rest. A corpse is preserved indefinitely. An enemy makes a Constitution save or takes acid damage and has disadvantage on its next attack. Enemy damage is 1d8, increasing to 2d8 at Cleric 5, 3d8 at 11 and 4d8 at 17. Uses equal your Wisdom modifier per long rest.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'funk-of-the-divine',
                    'label' => 'Channel Divinity: Funk of the Divine',
                    'level' => 2,
                    'summary' => 'Create a 15-foot fermentation aura for 1 minute: enemies risk 2d10 + Cleric level damage while allies gain +1d4 to Constitution saves and healing received.',
                    'detail' => 'Expend Channel Divinity as an action to create a 15-foot-radius fermentation aura centered on yourself for 1 minute, requiring concentration. Enemies beginning their turn in the aura make a Constitution save or take radiant or poison damage equal to 2d10 + your Cleric level and suffer disadvantage on Wisdom checks until the end of their next turn. Allies in the aura add 1d4 to Constitution saving throws and healing received.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'spiritual-brine',
                    'label' => 'Spiritual Brine',
                    'level' => 6,
                    'summary' => 'Gain acid and poison resistance, poisoned immunity, and once per round may react to turn poison damage within 30 feet into healing.',
                    'detail' => 'Gain resistance to acid and poison damage and immunity to the poisoned condition. Once per round, when a creature within 30 feet takes poison damage, you may use your reaction to change that damage into healing.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'pickled-spirits',
                    'label' => 'Pickled Spirits',
                    'level' => 8,
                    'summary' => 'Healing spells also grant temporary HP equal to your Wisdom modifier to every creature they heal.',
                    'detail' => 'When you cast a spell that restores hit points, each target also gains temporary hit points equal to your Wisdom modifier.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'mother-culture',
                    'label' => 'Mother Culture',
                    'level' => 17,
                    'summary' => 'Once per long rest, summon a 1-minute microbial presence that heals allies for 2d6, sheds conditions and punishes hostile creatures for 4d6.',
                    'detail' => 'As an action once per long rest, summon the Mother Culture for 1 minute. At the end of each of your turns, allies within 30 feet regain 2d6 hit points and shed one of poisoned, blinded, deafened, frightened or paralyzed. Hostile creatures in the area make a Constitution save each round or take 4d6 radiant or poison damage and have their speed halved until their next turn.',
                    'mode' => 'automatic',
                ],
            ],
        ],
    ];

    public static function forDomain(string $domainKey): self
    {
        $key = sanitize_key($domainKey);

        if (! isset(self::DOMAINS[$key])) {
            throw new InvalidArgumentException(
                'Unknown Cleric Divine Domain.'
            );
        }

        return new self($key);
    }

    /** @return array<int,self> */
    public static function allDefinitions(): array
    {
        return array_map(
            static fn (string $key): self =>
                new self($key),
            array_keys(self::DOMAINS)
        );
    }

    private function __construct(
        private string $domainKey
    ) {
    }

    public function supports(
        string $pathKey
    ): bool {
        return sanitize_key($pathKey)
            === $this->domainKey;
    }

    public function pathKey(): string
    {
        return $this->domainKey;
    }

    public function pathLabel(): string
    {
        return self::DOMAINS[
            $this->domainKey
        ]['label'];
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return self::DOMAINS[
            $this->domainKey
        ]['gifts'];
    }
}
