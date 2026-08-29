<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Canonical source transcription for Phase III.13.1A.
 *
 * Values are intentionally limited to what the Player's Handbook states.
 * Missing metadata remains null/empty rather than being inferred from 5e.
 * Duplicate handbook records are retained as source variants.
 *
 * @return array<int,array<string,mixed>>
 */
return [
    [
        'key' => 'shelfshine',
        'name' => 'Shelfshine',
        'kind' => 'renamed',
        'original_spell' => 'Light',
        'level' => 0,
        'school' => 'evocation',
        'access_labels' => ['artificer', 'bard', 'cleric', 'sorcerer', 'wizard'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => 'evocation',
                'access_labels' => ['artificer', 'bard', 'cleric', 'sorcerer', 'wizard'],
                'source_text' => 'Light → Shelfshine
You touch one object and make it shine with neat enchanted shelf-light for 1 hour.
Mechanics: The object sheds bright light in a 20-foot radius and dim light for an additional 20 feet. The light may be covered by an opaque object. Casting Shelfshine again ends the previous Shelfshine.',
            ],
        ],
    ],
    [
        'key' => 'cure-meats',
        'name' => 'Cure Meats',
        'kind' => 'renamed',
        'original_spell' => 'Cure Wounds',
        'level' => null,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['level-not-stated-in-handbook', 'school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => null,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Cure Wounds → Cure Meats
A burst of long-lasting magical sustenance restores vitality.
Mechanics: Same as Cure Wounds (1d8 + spellcasting modifier HP on touch).',
            ],
        ],
    ],
    [
        'key' => 'sip-of-vitality',
        'name' => 'Sip of Vitality',
        'kind' => 'renamed',
        'original_spell' => 'Healing Word',
        'level' => null,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['level-not-stated-in-handbook', 'school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => null,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Healing Word → Sip of Vitality
A word of refreshment, like the first taste of a revitalizing drink.
Mechanics: Same as Healing Word (1d4 + spellcasting modifier HP at range, bonus action).',
            ],
        ],
    ],
    [
        'key' => 'shelf-stable-salvation',
        'name' => 'Shelf-Stable Salvation',
        'kind' => 'renamed',
        'original_spell' => 'Mass Healing Word',
        'level' => null,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['level-not-stated-in-handbook', 'school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => null,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Mass Healing Word → Shelf-Stable Salvation
You distribute bite-sized boosts of healing to your allies.
Mechanics: Same as Mass Healing Word (up to 6 creatures regain 1d4 + modifier HP, bonus action).',
            ],
        ],
    ],
    [
        'key' => 'backroom-break',
        'name' => 'Backroom Break',
        'kind' => 'renamed',
        'original_spell' => 'Prayer of Healing',
        'level' => null,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['level-not-stated-in-handbook', 'school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => null,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Prayer of Healing → Backroom Break
A brief rest behind the scenes allows wounds to mend.
Mechanics: Same as Prayer of Healing (up to 6 creatures regain 2d8 + modifier HP, takes 10 minutes).',
            ],
        ],
    ],
    [
        'key' => 'hot-buffet-aura',
        'name' => 'Hot Buffet Aura',
        'kind' => 'renamed',
        'original_spell' => 'Aura of Vitality',
        'level' => null,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['level-not-stated-in-handbook', 'school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => null,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Aura of Vitality → Hot Buffet Aura
A restorative aroma and warmth surrounds you like a rotating heat lamp.
Mechanics: Same as Aura of Vitality (bonus action to heal 2d6 HP to a creature in 30 ft for 1 minute).',
            ],
        ],
    ],
    [
        'key' => 'deli-counter-spirit',
        'name' => 'Deli Counter Spirit',
        'kind' => 'renamed',
        'original_spell' => 'Healing Spirit',
        'level' => null,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['level-not-stated-in-handbook', 'school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => null,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Healing Spirit → Deli Counter Spirit
A cheerful, spectral butcher doles out restorative deli slices.
Mechanics: Same as Healing Spirit (a healing aura that restores 1d6 HP per turn to a creature in its space).',
            ],
        ],
    ],
    [
        'key' => 'snack-pack',
        'name' => 'Snack Pack',
        'kind' => 'renamed',
        'original_spell' => 'Goodberry',
        'level' => null,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['level-not-stated-in-handbook', 'school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => null,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Goodberry → Snack Pack
You conjure bite-sized nutritious treats ready for field use.
Mechanics: Same as Goodberry (10 berries restore 1 HP each, can be used to stabilize or nourish).',
            ],
        ],
    ],
    [
        'key' => 'antacid-infusion',
        'name' => 'Antacid Infusion',
        'kind' => 'renamed',
        'original_spell' => 'Lesser Restoration',
        'level' => null,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['level-not-stated-in-handbook', 'school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => null,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Lesser Restoration → Antacid Infusion
A fizzy burst neutralizes ailments and indigestion-like afflictions.
Mechanics: Same as Lesser Restoration (cures disease or one condition: blinded, deafened, paralyzed, or poisoned).',
            ],
        ],
    ],
    [
        'key' => 'mystery-mustard-missile',
        'name' => 'Mystery Mustard Missile',
        'kind' => 'renamed',
        'original_spell' => 'Magic Missile',
        'level' => 1,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Mystery Mustard Missile
(Magic Missile, 1st-level evocation)
Conjures several glowing darts of enchanted mustard that automatically strike foes. The effect is the same as Magic Missile, but each “dart” looks like a blob of flying deli mustard or spicy condiment.5esrd.com',
            ],
        ],
    ],
    [
        'key' => 'flame-grilled-fireball',
        'name' => 'Flame-Grilled Fireball',
        'kind' => 'renamed',
        'original_spell' => 'Fireball',
        'level' => 3,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 3,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Flame-Grilled Fireball
(Fireball, 3rd-level evocation)
Hurls a scorching soup can or flaming stew into the air. It explodes on impact in a burst of sizzling flame, like a pressure-cooker inferno. (Imagine a Supermarket clerk tossing an overheated chili pot!)5esrd.com',
            ],
        ],
    ],
    [
        'key' => 'sparkling-soda-wave',
        'name' => 'Sparkling Soda Wave',
        'kind' => 'renamed',
        'original_spell' => 'Thunderwave',
        'level' => 1,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Sparkling Soda Wave
(Thunderwave, 1st-level evocation)
A bubbly blast of carbonated soda erupts from your hands in a 15-foot cone. The fizzy wave knocks creatures back as if hit by a fizzy explosion of pop and ice.5esrd.com',
            ],
        ],
    ],
    [
        'key' => 'lightning-lemonade',
        'name' => 'Lightning Lemonade',
        'kind' => 'renamed',
        'original_spell' => 'Lightning Bolt',
        'level' => 3,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 3,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Lightning Lemonade
(Lightning Bolt, 3rd-level evocation)
Fires a crackling bolt of electrified lemonade. Envision squeezing a magical lemon into an electric socket: a streak of lightning flavored like lemonade zaps down a straight line.5esrd.com',
            ],
        ],
    ],
    [
        'key' => 'cone-of-cold-cones',
        'name' => 'Cone of Cold Cones',
        'kind' => 'renamed',
        'original_spell' => 'Cone of Cold',
        'level' => 5,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 5,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Cone of Cold Cones
(Cone of Cold, 5th-level evocation)
Unleashes a 60-foot cone of freezing ice-cream energy. Targets in the cone are encased in frost like an instant ice cream cone covering, dealing cold damage as normal.5esrd.com',
            ],
        ],
    ],
    [
        'key' => 'spicy-salsa-spread',
        'name' => 'Spicy Salsa Spread',
        'kind' => 'renamed',
        'original_spell' => 'Burning Hands',
        'level' => 1,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Spicy Salsa Spread
(Burning Hands, 1st-level evocation)
Flames erupt from your fingertips like a volcanic salsa dip. The cone of fire looks like a stream of molten nacho cheese or spicy salsa hurled at enemies5esrd.com.',
            ],
        ],
    ],
    [
        'key' => 'pepper-spray',
        'name' => 'Pepper Spray',
        'kind' => 'renamed',
        'original_spell' => 'Poison Spray',
        'level' => 0,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Pepper Spray
(Poison Spray, Cantrip)
You release a cloud of hot pepper powder from your hands in your foe’s face. It deals poison damage just like Poison Spray, with a flavorful pun on pepper spray.5esrd.com',
            ],
        ],
    ],
    [
        'key' => 'ice-cube-avalanche',
        'name' => 'Ice Cube Avalanche',
        'kind' => 'renamed',
        'original_spell' => 'Ice Storm',
        'level' => 4,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 4,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Ice Cube Avalanche
(Ice Storm, 4th-level evocation)
Calls down a storm of giant ice cubes and frosty grocery bag shreds in a 20-foot cylinder. Targets take slashing and cold damage as if bombarded by rolling freezers.5esrd.com',
            ],
        ],
    ],
    [
        'key' => 'vinegar-volley',
        'name' => 'Vinegar Volley',
        'kind' => 'renamed',
        'original_spell' => 'Acid Arrow',
        'level' => 2,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Vinegar Volley
(Acid Arrow, 2nd-level evocation)
You fire an arrow of caustic pickle brine (vinegar). The arrow splashes acidic pickle juice on impact, dealing acid damage over time like the normal Acid Arrow.',
            ],
        ],
    ],
    [
        'key' => 'candy-orb',
        'name' => 'Candy Orb',
        'kind' => 'renamed',
        'original_spell' => 'Chromatic Orb',
        'level' => 1,
        'school' => 'transmutation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'transmutation',
                'access_labels' => [],
                'source_text' => 'Candy Orb
(Chromatic Orb, 1st-level transmutation)
Hurls a colorful gumball of mana that explodes on impact. Choose an “element” by flavor: e.g. peppermint orb (cold), cinnamon orb (fire), etc. Works like Chromatic Orb with a candy twist.',
            ],
        ],
    ],
    [
        'key' => 'shard-of-shatter',
        'name' => 'Shard of Shatter',
        'kind' => 'renamed',
        'original_spell' => 'Shatter',
        'level' => 2,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Shard of Shatter
(Shatter, 2nd-level evocation)
A loud crash disperses a bomb of brittle baked goods (like a stacked tower of crackers or candy glass). The destructive dome of sound breaks enemies and objects as per the normal Shatter spell.',
            ],
        ],
    ],
    [
        'key' => 'bubble-wrap-vest',
        'name' => 'Bubble Wrap Vest',
        'kind' => 'renamed',
        'original_spell' => 'Mage Armor',
        'level' => 1,
        'school' => 'abjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'abjuration',
                'access_labels' => [],
                'source_text' => 'Bubble Wrap Vest
(Mage Armor, 1st-level abjuration)
You cover yourself in layers of protective bubble wrap and padded grocery sacks, granting the standard Mage Armor boost to AC. (It’s like wearing a crunchy, popping coat of insulation.)',
            ],
        ],
    ],
    [
        'key' => 'shopping-cart-shield',
        'name' => 'Shopping Cart Shield',
        'kind' => 'renamed',
        'original_spell' => 'Shield',
        'level' => 1,
        'school' => 'abjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'abjuration',
                'access_labels' => [],
                'source_text' => 'Shopping Cart Shield
(Shield, 1st-level abjuration)
An invisible aisle barrier springs up. You hold aloft an ethereal shopping cart or produce crate that grants +5 bonus to AC until your next turn, exactly like the Shield spell.',
            ],
        ],
    ],
    [
        'key' => 'mirror-glaze-mirage',
        'name' => 'Mirror Glaze Mirage',
        'kind' => 'renamed',
        'original_spell' => 'Mirror Image',
        'level' => 2,
        'school' => 'illusion',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'illusion',
                'access_labels' => [],
                'source_text' => 'Mirror Glaze Mirage
(Mirror Image, 2nd-level illusion)
Conjures illusory glazed donut reflections of yourself. Attackers see several glossy, frosted clones (like glazed doughnuts on a tray), making your true position hard to hit (mechanically identical to Mirror Image).',
            ],
        ],
    ],
    [
        'key' => 'berry-blur',
        'name' => 'Berry Blur',
        'kind' => 'renamed',
        'original_spell' => 'Blur',
        'level' => 2,
        'school' => 'illusion',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'illusion',
                'access_labels' => [],
                'source_text' => 'Berry Blur
(Blur, 2nd-level illusion)
Your form blurs into a swirl of flying berries. Attackers have disadvantage on hits (as with Blur) because you appear covered in swirling berry confetti.',
            ],
        ],
    ],
    [
        'key' => 'crust-armor',
        'name' => 'Crust Armor',
        'kind' => 'renamed',
        'original_spell' => 'Stoneskin',
        'level' => 4,
        'school' => 'abjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 4,
                'school' => 'abjuration',
                'access_labels' => [],
                'source_text' => 'Crust Armor
(Stoneskin, 4th-level abjuration)
Your skin hardens like crusty bread. You essentially gain resistance to nonmagical bludgeoning/slashing/piercing (as Stoneskin does), pictured as a tough crust shielding you.',
            ],
        ],
    ],
    [
        'key' => 'fortified-fridge',
        'name' => 'Fortified Fridge',
        'kind' => 'renamed',
        'original_spell' => 'Wall of Ice',
        'level' => 6,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 6,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Fortified Fridge
(Wall of Ice, 6th-level evocation)*
Creates a solid refrigerator wall or massive freezer slab of ice along a 60-foot line. The wall is as durable as Wall of Ice, but it looks like frosty appliance panels or stacked frozen food blocks.',
            ],
        ],
    ],
    [
        'key' => 'sanctuary-aisle',
        'name' => 'Sanctuary Aisle',
        'kind' => 'renamed',
        'original_spell' => 'Sanctuary',
        'level' => 1,
        'school' => 'abjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'abjuration',
                'access_labels' => [],
                'source_text' => 'Sanctuary Aisle
(Sanctuary, 1st-level abjuration)
You step into a protected supermarket aisle. Celestial shopping-cart angels guard you: attackers must succeed on a Wis save before attacking you, just like Sanctuary. The description fits a holy aisle filled with incense and prayer.',
            ],
        ],
    ],
    [
        'key' => 'arcane-coupon',
        'name' => 'Arcane Coupon',
        'kind' => 'renamed',
        'original_spell' => 'Counterspell',
        'level' => null,
        'school' => null,
        'access_labels' => [],
        'source_issues' => ['level-not-stated-in-handbook', 'school-not-stated-in-handbook', 'access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => null,
                'school' => null,
                'access_labels' => [],
                'source_text' => 'Arcane Coupon
(Counterspell, Reaction)
As a reaction, you flash a magical discount coupon that cancels an enemy spell in flight. It works exactly like Counterspell, but narratively it looks like slapping a “50% off – Void” coupon on the magical effect.',
            ],
        ],
    ],
    [
        'key' => 'deli-sanctuary',
        'name' => 'Deli Sanctuary',
        'kind' => 'renamed',
        'original_spell' => 'Warding Bond',
        'level' => 2,
        'school' => 'abjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'abjuration',
                'access_labels' => [],
                'source_text' => 'Deli Sanctuary
(Warding Bond, 2nd-level abjuration)
You bind yourself to an ally with enchanted deli twine and a shared loaf of hope. Like Warding Bond, the bond shares damage and grants +1 AC to the ally, flavored as “taking a slice of bread to protect a friend.”',
            ],
        ],
    ],
    [
        'key' => 'preservation-spray',
        'name' => 'Preservation Spray',
        'kind' => 'renamed',
        'original_spell' => 'Protection from Poison/Radiation',
        'level' => 2,
        'school' => 'abjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'abjuration',
                'access_labels' => [],
                'source_text' => 'Preservation Spray
(Protection from Poison/Radiation, 2nd-level abjuration)
You mist a target with shelf-preservative spray, giving advantage on saving throws against poison or radiation effects (if your setting has them), much like Protection from Poison. Flavor is as if “sealing them in a protective film.”',
            ],
        ],
    ],
    [
        'key' => 'spork-barrage',
        'name' => 'Spork Barrage',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 0,
        'school' => 'evocation',
        'access_labels' => ['Bard', 'Sorcerer', 'Wizard'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => 'evocation',
                'access_labels' => ['Bard', 'Sorcerer', 'Wizard'],
                'source_text' => 'Spork Barrage (Evocation, Bard/Sorcerer/Wizard)
You hurl a spectral spork at a creature within 30ft.
* Attack roll; on hit, it deals 1d6 force damage.
* At 5th level: 2 sporks (1d6 each), 11th: 3, 17th: 4.',
            ],
        ],
    ],
    [
        'key' => 'produce-puff',
        'name' => 'Produce Puff',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 0,
        'school' => 'transmutation',
        'access_labels' => ['Druid', 'Cleric'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => 'transmutation',
                'access_labels' => ['Druid', 'Cleric'],
                'source_text' => 'Produce Puff (Transmutation, Druid/Cleric)
You conjure a puff of veggie vapor.
* Target creature in 10 ft makes a Con save or gets disadvantage on its next Strength check or attack.',
            ],
        ],
    ],
    [
        'key' => 'toss-salad',
        'name' => 'Toss Salad',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 0,
        'school' => null,
        'access_labels' => ['Druid', 'Bard'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => null,
                'access_labels' => ['Druid', 'Bard'],
                'source_text' => 'Toss Salad (Druid, Bard)
Casting Time: 1 action
Range: 30 feet
Duration: Concentration, up to 1 minute
Effect: You summon a small whirlwind of leafy greens. Choose a creature within range. It must succeed on a Dexterity saving throw or be lightly entangled by leaves, reducing its movement speed by 10 feet and giving disadvantage on Stealth checks. On a failed save at the start of its next turn, it also takes 1d6 bludgeoning damage from a flying tomato.',
            ],
        ],
    ],
    [
        'key' => 'garlic-puff',
        'name' => 'Garlic Puff',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 0,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Garlic Puff
Evocation cantrip
Casting Time: 1 action
Range: 15 ft cone
Components: V
Duration: Instantaneous
You exhale a cone of acrid garlic vapor. Creatures in the area must succeed on a Constitution saving throw or begin coughing until the start of your next turn, giving them disadvantage on opportunity attacks and verbal spellcasting.',
            ],
        ],
    ],
    [
        'key' => 'lettuce-wrap',
        'name' => 'Lettuce Wrap',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 0,
        'school' => 'transmutation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => 'transmutation',
                'access_labels' => [],
                'source_text' => 'Lettuce Wrap
Transmutation cantrip
Casting Time: 1 bonus action
Range: Touch
Components: V, S
Duration: 1 round
You summon a leafy shield around a creature. Until the start of your next turn, the target gains +1 to AC. If they are hit, the wrap splatters dramatically, dealing 1 acid damage to the attacker.',
            ],
        ],
    ],
    [
        'key' => 'sugar-snap',
        'name' => 'Sugar Snap',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 0,
        'school' => 'evocation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => 'evocation',
                'access_labels' => [],
                'source_text' => 'Sugar Snap
Evocation cantrip
Casting Time: 1 action
Range: 60 ft
Components: V, S
Duration: Instantaneous
You conjure and snap a hard candy shard at an enemy. Make a ranged spell attack. On a hit, the target takes 1d6 force damage and must make a Strength save or be knocked 5 feet back.
* At higher levels: Damage increases by 1d6 at 5th, 11th, and 17th levels.',
            ],
        ],
    ],
    [
        'key' => 'perk-up',
        'name' => 'Perk Up',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 0,
        'school' => 'enchantment',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => 'enchantment',
                'access_labels' => [],
                'source_text' => 'Perk Up
Enchantment cantrip
Casting Time: 1 action
Range: 30 ft
Components: V
Duration: 1 minute
You channel the invigorating power of a hot beverage. One willing creature gains advantage on its next saving throw against being charmed or frightened. Once affected, the creature cannot benefit from this cantrip again until they finish a short rest.',
            ],
        ],
    ],
    [
        'key' => 'curdle',
        'name' => 'Curdle',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 0,
        'school' => 'necromancy',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => 'necromancy',
                'access_labels' => [],
                'source_text' => 'Curdle
Necromancy cantrip
Casting Time: 1 action
Range: 30 ft
Components: V, S
Duration: Instantaneous
You send a wave of spoilage at a liquid or creamy object.
* On an object: It curdles or spoils.
* On a creature: They must make a Constitution save or suffer disadvantage on the next attack they make before the end of their next turn.',
            ],
        ],
    ],
    [
        'key' => 'peel-flick',
        'name' => 'Peel Flick',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 0,
        'school' => 'conjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 0,
                'school' => 'conjuration',
                'access_labels' => [],
                'source_text' => 'Peel Flick
Conjuration cantrip
Casting Time: 1 action
Range: 30 ft
Components: S
Duration: Instantaneous
You magically flick a banana peel or vegetable skin under a target’s feet. Make a ranged spell attack. On a hit, the creature must succeed on a Dexterity save or fall prone.',
            ],
        ],
    ],
    [
        'key' => 'juicebox-rejuvenation',
        'name' => 'Juicebox Rejuvenation',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 1,
        'school' => 'conjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'conjuration',
                'access_labels' => [],
                'source_text' => 'Juicebox Rejuvenation
1st-level Conjuration
Range: 30 ft | Components: V, S | Duration: Instantaneous
You toss a magical juicebox to a creature, bursting with sugary vitality.
* The creature regains 1d8 + your spellcasting modifier hit points.
* If the creature is below half HP, it also gains +1 to AC until the start of your next turn.
* Higher levels: +1d8 per slot above 1st.',
            ],
        ],
    ],
    [
        'key' => 'garlic-ward',
        'name' => 'Garlic Ward',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 1,
        'school' => 'abjuration',
        'access_labels' => ['Cleric', 'Druid'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'abjuration',
                'access_labels' => ['Cleric', 'Druid'],
                'source_text' => 'Garlic Ward (Abjuration, Cleric/Druid)
Create a 10ft aura of pungent garlic.
* Enemies entering the area must make a Con save or be poisoned until end of turn.
* Allies inside the aura are immune to charm effects.',
            ],
        ],
    ],
    [
        'key' => 'slicewind',
        'name' => 'Slicewind',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 1,
        'school' => 'evocation',
        'access_labels' => ['Ranger', 'Sorcerer'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'evocation',
                'access_labels' => ['Ranger', 'Sorcerer'],
                'source_text' => 'Slicewind (Evocation, Ranger/Sorcerer)
Summon a spectral mandoline blade in a 15ft line.
* Dex save or take 2d8 slashing damage. Half on save.
* Adds 1d6 extra to plant-based enemies.',
            ],
        ],
    ],
    [
        'key' => 'reheat',
        'name' => 'Reheat',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 1,
        'school' => 'evocation',
        'access_labels' => ['Sorcerer', 'Wizard', 'Artificer'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'evocation',
                'access_labels' => ['Sorcerer', 'Wizard', 'Artificer'],
                'source_text' => 'Reheat
1st-level evocation
Casting Time: 1 action
Range: 30 feet
Components: V, S
Duration: Concentration, up to 1 minute
Classes: Sorcerer, Wizard, Artificer
Choose one creature or object. It begins to steadily heat up. If a creature, it takes 1d6 fire damage at the start of each turn. If metal, it glows red-hot and counts as heated metal for the purpose of interactions (disadvantage on attacks, etc.).
At Higher Levels. Add 1d6 fire per level above 1st.',
            ],
        ],
    ],
    [
        'key' => 'sugarstep',
        'name' => 'Sugarstep',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 1,
        'school' => 'conjuration',
        'access_labels' => ['Bard', 'Sorcerer', 'Marshmallow Folk'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => 'conjuration',
                'access_labels' => ['Bard', 'Sorcerer', 'Marshmallow Folk'],
                'source_text' => 'Sugarstep
1st-level conjuration
Casting Time: 1 bonus action
Range: Self
Components: V
Duration: 10 minutes
Classes: Bard, Sorcerer, Marshmallow Folk
Your footsteps leave behind trails of sugar and sparkles. While active:
* Difficult terrain doesn\'t slow you.
* You can Dash as a bonus action.
* Anyone trying to follow your trail must succeed on a DC 13 Wisdom (Survival) check or slip and fall prone.',
            ],
        ],
    ],
    [
        'key' => 'salt-the-earth',
        'name' => 'Salt the Earth',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 1,
        'school' => null,
        'access_labels' => ['Cleric', 'Warlock'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => null,
                'access_labels' => ['Cleric', 'Warlock'],
                'source_text' => 'Salt the Earth (Cleric, Warlock)
Casting Time: 1 action
Range: 60 feet
Duration: 1 minute
Effect: You curse a 10-foot radius of ground. Creatures in that area must make a Constitution saving throw or be poisoned for 1 minute. Creatures that fail by 5 or more are also unable to regain hit points until the end of their next turn.',
            ],
        ],
    ],
    [
        'key' => 'searing-skillet',
        'name' => 'Searing Skillet',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 1,
        'school' => null,
        'access_labels' => ['Sorcerer', 'Wizard', 'Artificer'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 1,
                'school' => null,
                'access_labels' => ['Sorcerer', 'Wizard', 'Artificer'],
                'source_text' => 'Searing Skillet (Sorcerer, Wizard, Artificer)
Casting Time: 1 bonus action
Range: 30 feet
Duration: Instantaneous
Effect: You summon a hot, cast-iron skillet and hurl it magically. Make a ranged spell attack. On a hit, the target takes 2d6 fire damage and must make a Strength saving throw or be knocked prone.',
            ],
        ],
    ],
    [
        'key' => 'season-to-taste',
        'name' => 'Season to Taste',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 2,
        'school' => 'transmutation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'transmutation',
                'access_labels' => [],
                'source_text' => 'Season to Taste
2nd-level Transmutation
Range: Touch | Components: V, S | Duration: 1 hour
You sprinkle magical seasoning over a creature.
* The target gains advantage on saving throws against poison and disease.
* Once during the duration, the creature can reroll a failed Constitution save (they must accept the second roll).
* Has no effect on constructs or undead.',
            ],
        ],
    ],
    [
        'key' => 'bread-wall',
        'name' => 'Bread Wall',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 2,
        'school' => 'conjuration',
        'access_labels' => ['Druid', 'Wizard'],
        'source_issues' => ['conflicting-source-variants'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'conjuration',
                'access_labels' => ['Druid', 'Wizard'],
                'source_text' => 'Bread Wall (Conjuration, Druid/Wizard)
Raise a 20ft long, 10ft high, 5ft thick wall of magically hardened bread.
* Counts as full cover, lasts 1 minute.
* Fire damage burns through 5 ft per 10 damage.',
            ],
            [
                'source_variant' => 2,
                'level' => 2,
                'school' => null,
                'access_labels' => ['Druid', 'Cleric', 'Wizard'],
                'source_text' => 'Bread Wall (Druid, Cleric, Wizard)
Casting Time: 1 action
Range: 60 feet
Duration: 1 minute
Effect: You create a 5-ft thick, 10x10 wall of crusty sourdough. It provides three-quarters cover, can be walked on as difficult terrain, and has AC 13, 20 HP per 5-ft section. It crumbles into crumbs when destroyed.',
            ],
        ],
    ],
    [
        'key' => 'skewer-lance',
        'name' => 'Skewer Lance',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 2,
        'school' => 'evocation',
        'access_labels' => ['Sorcerer', 'Fighter', 'Artificer'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'evocation',
                'access_labels' => ['Sorcerer', 'Fighter', 'Artificer'],
                'source_text' => 'Skewer Lance (Evocation, Sorcerer/Fighter/Artificer)
Create a glowing skewer that pierces in a 30ft line.
* Enemies in line make a Dex save or take 3d10 piercing damage and are pulled 10 ft toward you.
* Critical hits impale them (grappled until end of next turn).',
            ],
        ],
    ],
    [
        'key' => 'sproutshield',
        'name' => 'Sproutshield',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 2,
        'school' => 'abjuration',
        'access_labels' => ['Druid', 'Cleric (Fermentation)', 'Ranger'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'abjuration',
                'access_labels' => ['Druid', 'Cleric (Fermentation)', 'Ranger'],
                'source_text' => 'Sproutshield
2nd-level abjuration
Casting Time: 1 reaction (when you take damage)
Range: Self
Components: V, S
Duration: 1 round
Classes: Druid, Cleric (Fermentation), Ranger
A living shield of plant matter erupts around you, reducing incoming damage by 2d8 and giving +2 AC until the start of your next turn. If reduced to 0 HP by the triggering attack, you instead stabilize with 1 HP as roots hold you together.',
            ],
        ],
    ],
    [
        'key' => 'compost-blast',
        'name' => 'Compost Blast',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 2,
        'school' => 'necrotic/conjuration hybrid',
        'access_labels' => ['Druid', 'Warlock (Spoil Pact)', 'Cleric (Fermentation)'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => 'necrotic/conjuration hybrid',
                'access_labels' => ['Druid', 'Warlock (Spoil Pact)', 'Cleric (Fermentation)'],
                'source_text' => 'Compost Blast
2nd-level necrotic/conjuration hybrid
Casting Time: 1 action
Range: 60 ft (20-ft radius)
Components: V, S, M (a rotten fruit)
Duration: Instantaneous
Classes: Druid, Warlock (Spoil Pact), Cleric (Fermentation)
You explode a sphere of rich compost and rot. Creatures in range must make a Con save:
* Fail = 4d8 necrotic damage + poisoned for 1 turn.
* Success = half damage, no poison.
Non-magical plants in the area grow wildly or wilt (your choice).',
            ],
        ],
    ],
    [
        'key' => 'pickling-curse',
        'name' => 'Pickling Curse',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 2,
        'school' => null,
        'access_labels' => ['Warlock', 'Bard'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => null,
                'access_labels' => ['Warlock', 'Bard'],
                'source_text' => 'Pickling Curse (Warlock, Bard)
Casting Time: 1 action
Range: 60 feet
Duration: Concentration, up to 1 minute
Effect: A target must make a Constitution saving throw or be magically pickled. While pickled, it is restrained and immune to poison damage, but takes double acid damage. At the end of each of its turns, it repeats the save.',
            ],
        ],
    ],
    [
        'key' => 'bubbling-broth',
        'name' => 'Bubbling Broth',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 2,
        'school' => null,
        'access_labels' => ['Wizard', 'Sorcerer', 'Druid'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 2,
                'school' => null,
                'access_labels' => ['Wizard', 'Sorcerer', 'Druid'],
                'source_text' => 'Bubbling Broth (Wizard, Sorcerer, Druid)
Casting Time: 1 action
Range: Self (30-ft aura)
Duration: Concentration, up to 1 minute
Effect: You summon a simmering aura of broth. Hostile creatures that enter or start their turn in the aura must make a Dex save or take 3d6 fire damage. Allies regain 1d4 HP at the start of their turn while in the aura.',
            ],
        ],
    ],
    [
        'key' => 'preserve-freshness',
        'name' => 'Preserve Freshness',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 3,
        'school' => 'abjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 3,
                'school' => 'abjuration',
                'access_labels' => [],
                'source_text' => 'Preserve Freshness
3rd-level Abjuration
Range: Touch | Components: V, S, M (bit of wax paper) | Duration: 8 hours
You create a preservation aura that halts spoilage and decay.
* Prevents death saves from deteriorating.
* A dying creature affected by this spell has resistance to all damage while unconscious.
* Once per creature per casting, it automatically stabilizes a dying target (as spare the dying).',
            ],
        ],
    ],
    [
        'key' => 'fondue-flood',
        'name' => 'Fondue Flood',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 3,
        'school' => 'conjuration',
        'access_labels' => ['Wizard', 'Sorcerer'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 3,
                'school' => 'conjuration',
                'access_labels' => ['Wizard', 'Sorcerer'],
                'source_text' => 'Fondue Flood (Conjuration, Wizard/Sorcerer)
Call forth a sticky torrent of molten cheese.
* 20-ft radius. Dex save or take 5d6 fire and be restrained.
* Area becomes difficult terrain for 1 minute.',
            ],
        ],
    ],
    [
        'key' => 'flash-pickle',
        'name' => 'Flash Pickle',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 3,
        'school' => 'necromancy',
        'access_labels' => ['Druid', 'Wizard'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 3,
                'school' => 'necromancy',
                'access_labels' => ['Druid', 'Wizard'],
                'source_text' => 'Flash Pickle (Necromancy, Druid/Wizard)
Rapidly pickle a corpse, preserving it perfectly.
* Delays decay indefinitely and can be used to revive a creature with Revivify even after 1 hour.',
            ],
        ],
    ],
    [
        'key' => 'vacuum-seal',
        'name' => 'Vacuum Seal',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 3,
        'school' => 'transmutation',
        'access_labels' => ['Wizard', 'Artificer', 'Warlock'],
        'source_issues' => ['conflicting-source-variants'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 3,
                'school' => 'transmutation',
                'access_labels' => ['Wizard', 'Artificer', 'Warlock'],
                'source_text' => 'Vacuum Seal
3rd-level transmutation
Casting Time: 1 action
Range: 60 feet
Components: V, S
Duration: 1 minute
Classes: Wizard, Artificer, Warlock
You create an invisible pressure field around a creature or object, sealing it in airtight magical wrapping.
* The creature is restrained (Str save ends at end of turn).
* Flying creatures fall.
* If the target is undead, construct, or elemental, it also takes 3d6 force damage per round as the seal crushes them.',
            ],
            [
                'source_variant' => 2,
                'level' => 4,
                'school' => null,
                'access_labels' => ['Wizard', 'Artificer'],
                'source_text' => 'Vacuum Seal (Wizard, Artificer)
Casting Time: 1 action
Range: Touch (or self)
Duration: 8 hours
Effect: You magically seal a creature or object in an invisible vacuum wrap. While sealed, they:
* Cannot be poisoned
* Don’t age
* Can’t be detected by scrying or divination
Unsealing requires a command word or Dispel Magic.',
            ],
        ],
    ],
    [
        'key' => 'grease-golem',
        'name' => 'Grease Golem',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 3,
        'school' => null,
        'access_labels' => ['Artificer', 'Wizard', 'Sorcerer'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 3,
                'school' => null,
                'access_labels' => ['Artificer', 'Wizard', 'Sorcerer'],
                'source_text' => 'Grease Golem (Artificer, Wizard, Sorcerer)
Casting Time: 1 action
Range: 30 feet
Duration: Concentration, up to 1 minute
Effect: You animate a large blob of sentient grease (AC 13, HP 25, immune to bludgeoning, 1 slam attack: +5 to hit, 2d6 + 2 bludgeoning). It is incredibly slippery — enemies within 5 feet must make a Dex save or fall prone each turn.',
            ],
        ],
    ],
    [
        'key' => 'seasoning-smite',
        'name' => 'Seasoning Smite',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 3,
        'school' => null,
        'access_labels' => ['Paladin', 'Cleric'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 3,
                'school' => null,
                'access_labels' => ['Paladin', 'Cleric'],
                'source_text' => 'Seasoning Smite (Paladin, Cleric)
Casting Time: 1 bonus action
Range: Self
Duration: 1 minute
Effect: Your weapon becomes infused with mystical seasonings. Your next melee hit deals an extra 4d8 radiant or necrotic damage (your choice), and the target must succeed on a Wisdom save or be blinded until the end of your next turn.',
            ],
        ],
    ],
    [
        'key' => 'mystic-label-swap',
        'name' => 'Mystic Label Swap',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 3,
        'school' => null,
        'access_labels' => ['Wizard', 'Bard'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 3,
                'school' => null,
                'access_labels' => ['Wizard', 'Bard'],
                'source_text' => 'Mystic Label Swap (Wizard, Bard)
Casting Time: 1 reaction (when targeted by a spell)
Range: 60 feet
Duration: Instantaneous
Effect: You magically swap spell targets. The triggering spell instead targets a creature or object of your choice within 60 feet, who must now make any saving throws or checks instead of you.',
            ],
        ],
    ],
    [
        'key' => 'sardine-swarm',
        'name' => 'Sardine Swarm',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 4,
        'school' => 'conjuration',
        'access_labels' => ['Druid', 'Sorcerer'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 4,
                'school' => 'conjuration',
                'access_labels' => ['Druid', 'Sorcerer'],
                'source_text' => 'Sardine Swarm (Conjuration, Druid/Sorcerer)
Summon a school of spectral sardines to swarm a 10 ft radius.
* Enemies must make a Dex save or take 6d8 psychic damage and fall prone, overwhelmed by fishy illusions.
* Creatures with Keen Smell have disadvantage on the save.',
            ],
        ],
    ],
    [
        'key' => 'meatstorm',
        'name' => 'Meatstorm',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 4,
        'school' => null,
        'access_labels' => ['Druid', 'Sorcerer', 'Warlock'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 4,
                'school' => null,
                'access_labels' => ['Druid', 'Sorcerer', 'Warlock'],
                'source_text' => 'Meatstorm (Druid, Sorcerer, Warlock)
Casting Time: 1 action
Range: 120 feet (20-ft radius)
Duration: Concentration, up to 1 minute
Effect: You rain down magically enhanced cuts of meat and bones. Creatures in the area must make a Dex save each turn or take 6d6 bludgeoning and slashing damage, and be pushed 10 ft.',
            ],
        ],
    ],
    [
        'key' => 'knife-storm',
        'name' => 'Knife Storm',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 4,
        'school' => 'conjuration',
        'access_labels' => ['Wizard', 'Fighter (Culinary Knight)', 'Artificer'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 4,
                'school' => 'conjuration',
                'access_labels' => ['Wizard', 'Fighter (Culinary Knight)', 'Artificer'],
                'source_text' => 'Knife Storm
4th-level conjuration
Casting Time: 1 action
Range: 60 feet (20-ft radius cylinder)
Components: V, S
Duration: Concentration, up to 1 minute
Classes: Wizard, Fighter (Culinary Knight), Artificer
Summon a whirling storm of spectral cutlery (forks, knives, cleavers). At the start of each creature’s turn in the area, they take 4d10 slashing damage (Dex save for half). The storm counts as heavily obscured terrain and extinguishes unprotected flames.',
            ],
        ],
    ],
    [
        'key' => 'oven-of-annihilation',
        'name' => 'Oven of Annihilation',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 5,
        'school' => 'evocation',
        'access_labels' => ['Wizard', 'Arificer'],
        'source_issues' => ['source-label-preserved:Arificer'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 5,
                'school' => 'evocation',
                'access_labels' => ['Wizard', 'Arificer'],
                'source_text' => 'Oven of Annihilation (Evocation, Wizard/Arificer)
A magical convection oven descends at a point you choose.
* Any creature that starts its turn within 10 ft takes 4d8 fire damage.
* You can "bake" an ally\'s weapon as a bonus action—next hit deals +3d8 radiant damage.',
            ],
        ],
    ],
    [
        'key' => 'brothbind',
        'name' => 'Brothbind',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 5,
        'school' => 'enchantment',
        'access_labels' => ['Cleric', 'Sorcerer', 'Druid', 'Warlock'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 5,
                'school' => 'enchantment',
                'access_labels' => ['Cleric', 'Sorcerer', 'Druid', 'Warlock'],
                'source_text' => 'Brothbind
5th-level enchantment
Casting Time: 1 action
Range: 90 ft
Components: V, S, M (a spoon)
Duration: Concentration, 1 minute
Classes: Cleric, Sorcerer, Druid, Warlock
You bind the life essence of up to 3 creatures in a shared “soup” of vitality. While bound:
* When one takes damage, the others share it evenly.
* If one is healed, the healing is split.
* All affected must be within 30 ft of each other.',
            ],
        ],
    ],
    [
        'key' => 'divine-fermentation',
        'name' => 'Divine Fermentation',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 5,
        'school' => null,
        'access_labels' => ['Cleric', 'Druid'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 5,
                'school' => null,
                'access_labels' => ['Cleric', 'Druid'],
                'source_text' => 'Divine Fermentation (Cleric, Druid)
Casting Time: 1 minute
Range: 60 feet
Duration: 1 hour
Effect: You infuse a barrel, jug, or cauldron with blessed fermentation. Any creature who drinks the elixir:
* Heals 6d8 HP
* Is cured of one curse or disease
* Gains resistance to necrotic damage for 1 hour
Each barrel can serve up to 8 doses.',
            ],
        ],
    ],
    [
        'key' => 'aisle-collapse',
        'name' => 'Aisle Collapse',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 5,
        'school' => null,
        'access_labels' => ['Wizard', 'Sorcerer', 'Warlock'],
        'source_issues' => ['school-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 5,
                'school' => null,
                'access_labels' => ['Wizard', 'Sorcerer', 'Warlock'],
                'source_text' => 'Aisle Collapse (Wizard, Sorcerer, Warlock)
Casting Time: 1 action
Range: 100 feet
Duration: Instantaneous
Effect: You magically collapse supermarket shelving on your enemies. Choose a 10x40 ft area. Creatures within must make a Dex save or take 8d10 force damage and be restrained by fallen debris.',
            ],
        ],
    ],
    [
        'key' => 'meatquake',
        'name' => 'Meatquake',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 6,
        'school' => 'evocation',
        'access_labels' => ['Druid', 'Sorcerer', 'Paladin (Order of the Flame Grill)', 'Warlock'],
        'source_issues' => [],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 6,
                'school' => 'evocation',
                'access_labels' => ['Druid', 'Sorcerer', 'Paladin (Order of the Flame Grill)', 'Warlock'],
                'source_text' => 'Meatquake
6th-level evocation
Casting Time: 1 action
Range: 150 ft (40-ft radius)
Components: V, S, M (a bone)
Duration: Instantaneous
Classes: Druid, Sorcerer, Paladin (Order of the Flame Grill), Warlock
You summon the primal energy of meat itself. The ground shudders as bones rise and slam down:
* Creatures must make a Dex save or take 8d10 bludgeoning and fall prone.
* Creatures made of meat (including humanoids and beasts) have disadvantage on the save.
* The area becomes difficult terrain for 1 hour, strewn with bones and sinew.',
            ],
        ],
    ],
    [
        'key' => 'essence-of-the-elixir',
        'name' => 'Essence of the Elixir',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 6,
        'school' => 'transmutation',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 6,
                'school' => 'transmutation',
                'access_labels' => [],
                'source_text' => 'Essence of the Elixir
6th-level Transmutation
Casting Time: 1 action
Range: Touch
Components: V, S, M (a drop of nectar)
Duration: Concentration, up to 10 minutes
You transform a potion or beverage into a radiant aura that imbues up to 3 willing creatures within 30 feet. Choose one potion (e.g., healing, resistance): its effect is distributed evenly across the targets at the start of their turn.
* If it’s a healing potion, each creature regains 2d4 + 2 HP every turn.
* If it’s resistance, all affected gain that resistance for the duration.
The potion is consumed upon casting.',
            ],
        ],
    ],
    [
        'key' => 'bread-golem',
        'name' => 'Bread Golem',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 6,
        'school' => 'conjuration',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 6,
                'school' => 'conjuration',
                'access_labels' => [],
                'source_text' => 'Bread Golem
6th-level Conjuration
Casting Time: 1 minute
Range: 30 feet
Components: V, S, M (a stale loaf and a pinch of yeast)
Duration: 1 hour
You bake a magical creature of bread and arcane yeast. It appears in an unoccupied space within range and obeys your commands.
Bread Golem
AC 16
HP 110
+7 to hit
2d10 + 5 bludgeoning damage
speed 30 ft
immune to poison
Immune to charm.
* As a bonus action, you can command it to explode in crumbs: all creatures within 10 ft must make a Dex save or fall prone.
After 1 hour or when reduced to 0 HP, it crumbles deliciously.',
            ],
        ],
    ],
    [
        'key' => 'honeytrap-hallucination',
        'name' => 'Honeytrap Hallucination',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 6,
        'school' => 'illusion',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 6,
                'school' => 'illusion',
                'access_labels' => [],
                'source_text' => 'Honeytrap Hallucination
6th-level Illusion
Casting Time: 1 action
Range: 60 feet
Components: V, S
Duration: Concentration, up to 1 minute
You summon the illusion of a delicious, glistening feast. All creatures in a 30-foot-radius sphere must succeed on a Wisdom save or become incapacitated as they hallucinate vivid, tempting food and lose track of reality.
* On a failure, they spend their action each turn attempting to “eat” or “grab” the illusion.
* Creatures may repeat the save at the end of each of their turns.
* If they are harmed, they automatically shake off the illusion.',
            ],
        ],
    ],
    [
        'key' => 'aura-of-onion-wrath',
        'name' => 'Aura of Onion Wrath',
        'kind' => 'marketrealm-original',
        'original_spell' => null,
        'level' => 6,
        'school' => 'necromancy',
        'access_labels' => [],
        'source_issues' => ['access-not-stated-in-handbook'],
        'variants' => [
            [
                'source_variant' => 1,
                'level' => 6,
                'school' => 'necromancy',
                'access_labels' => [],
                'source_text' => 'Aura of Onion Wrath
6th-level Necromancy
Casting Time: 1 action
Range: Self (30-foot radius)
Components: V, S
Duration: Concentration, up to 1 minute
You erupt in waves of bitter vapor. All creatures of your choice within 30 feet must make a Constitution saving throw at the start of their turn or suffer:
* 3d10 necrotic damage
* Blinded until the start of their next turn
* On success: half damage, no blindness.
Additionally, enemies that end their turn in the area lose reactions until their next turn from tearful agony.',
            ],
        ],
    ],
];
