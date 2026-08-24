<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Phase III.13.4 mundane Armoury.
 *
 * `handbook-mentioned` means the item is explicitly named by class starting
 * equipment/proficiency text in The Great Marketrealm - Players Handbook.
 * Mechanical profiles remain the Companion's standard-compatible mundane
 * profiles unless separately certified by a later handbook equipment table.
 *
 * `standard-compatible` means useful non-magical fantasy equipment added to
 * broaden the Companion; it is not represented as Marketrealm-handbook canon.
 */
return [
    // Simple melee weapons.
    ['club', 'Club', 'weapon', 'A plain hardwood club suitable for close fighting.', 2.0, 'standard-compatible', 'main-hand', '1d4', 'bludgeoning', null, null, 0, ['light'], 'Melee · 5 ft'],
    ['dagger', 'Dagger', 'weapon', 'A compact blade useful in a fight or around camp.', 1.0, 'handbook-mentioned', 'main-hand', '1d4', 'piercing', null, null, 0, ['finesse', 'light', 'thrown'], 'Thrown · 20/60 ft'],
    ['greatclub', 'Greatclub', 'weapon', 'A heavy two-handed club built for force rather than finesse.', 10.0, 'standard-compatible', 'main-hand', '1d8', 'bludgeoning', null, null, 0, ['two-handed'], 'Melee · 5 ft'],
    ['handaxe', 'Handaxe', 'weapon', 'A compact axe balanced for chopping or throwing.', 2.0, 'standard-compatible', 'main-hand', '1d6', 'slashing', null, null, 0, ['light', 'thrown'], 'Thrown · 20/60 ft'],
    ['javelin', 'Javelin', 'weapon', 'A light spear designed to be thrown at short range.', 2.0, 'standard-compatible', 'main-hand', '1d6', 'piercing', null, null, 0, ['thrown'], 'Thrown · 30/120 ft'],
    ['light-hammer', 'Light Hammer', 'weapon', 'A small war hammer that can be thrown in a pinch.', 2.0, 'standard-compatible', 'main-hand', '1d4', 'bludgeoning', null, null, 0, ['light', 'thrown'], 'Thrown · 20/60 ft'],
    ['mace', 'Mace', 'weapon', 'A sturdy metal-headed weapon for close combat.', 4.0, 'standard-compatible', 'main-hand', '1d6', 'bludgeoning', null, null, 0, [], 'Melee · 5 ft'],
    ['quarterstaff', 'Quarterstaff', 'weapon', 'A long hardwood staff equally useful on the road and in battle.', 4.0, 'standard-compatible', 'main-hand', '1d6', 'bludgeoning', null, null, 0, ['versatile'], 'Melee · 5 ft'],
    ['sickle', 'Sickle', 'weapon', 'A hooked harvesting blade repurposed for close combat.', 2.0, 'standard-compatible', 'main-hand', '1d4', 'slashing', null, null, 0, ['light'], 'Melee · 5 ft'],
    ['spear', 'Spear', 'weapon', 'A practical pole weapon that can be thrust or thrown.', 3.0, 'standard-compatible', 'main-hand', '1d6', 'piercing', null, null, 0, ['thrown', 'versatile'], 'Thrown · 20/60 ft'],

    // Simple ranged weapons.
    ['light-crossbow', 'Light Crossbow', 'weapon', 'A compact crossbow for deliberate ranged shots.', 5.0, 'standard-compatible', 'main-hand', '1d8', 'piercing', null, null, 0, ['ammunition', 'loading', 'ranged', 'two-handed'], 'Ranged · 80/320 ft'],
    ['dart', 'Dart', 'weapon', 'A small weighted missile made for quick throws.', 0.25, 'standard-compatible', 'main-hand', '1d4', 'piercing', null, null, 0, ['finesse', 'thrown'], 'Thrown · 20/60 ft'],
    ['shortbow', 'Shortbow', 'weapon', 'A light bow suited to mobile skirmishing.', 2.0, 'handbook-mentioned', 'main-hand', '1d6', 'piercing', null, null, 0, ['ammunition', 'ranged', 'two-handed'], 'Ranged · 80/320 ft'],
    ['sling', 'Sling', 'weapon', 'A simple sling for stones or shaped shot.', 0.0, 'standard-compatible', 'main-hand', '1d4', 'bludgeoning', null, null, 0, ['ammunition', 'ranged'], 'Ranged · 30/120 ft'],

    // Martial melee weapons.
    ['battleaxe', 'Battleaxe', 'weapon', 'A broad-headed martial axe with a powerful cutting edge.', 4.0, 'standard-compatible', 'main-hand', '1d8', 'slashing', null, null, 0, ['versatile'], 'Melee · 5 ft'],
    ['flail', 'Flail', 'weapon', 'A weighted striking head carried on a short chain.', 2.0, 'standard-compatible', 'main-hand', '1d8', 'bludgeoning', null, null, 0, [], 'Melee · 5 ft'],
    ['glaive', 'Glaive', 'weapon', 'A long polearm tipped with a sweeping blade.', 6.0, 'standard-compatible', 'main-hand', '1d10', 'slashing', null, null, 0, ['heavy', 'reach', 'two-handed'], 'Melee · 10 ft'],
    ['greataxe', 'Greataxe', 'weapon', 'A massive two-handed axe built for devastating swings.', 7.0, 'standard-compatible', 'main-hand', '1d12', 'slashing', null, null, 0, ['heavy', 'two-handed'], 'Melee · 5 ft'],
    ['greatsword', 'Greatsword', 'weapon', 'A large two-handed sword with tremendous cutting power.', 6.0, 'standard-compatible', 'main-hand', '2d6', 'slashing', null, null, 0, ['heavy', 'two-handed'], 'Melee · 5 ft'],
    ['halberd', 'Halberd', 'weapon', 'A long-shafted blade combining reach with heavy striking power.', 6.0, 'standard-compatible', 'main-hand', '1d10', 'slashing', null, null, 0, ['heavy', 'reach', 'two-handed'], 'Melee · 10 ft'],
    ['lance', 'Lance', 'weapon', 'A long cavalry weapon designed for powerful thrusts.', 6.0, 'standard-compatible', 'main-hand', '1d12', 'piercing', null, null, 0, ['reach', 'special'], 'Melee · 10 ft'],
    ['longsword', 'Longsword', 'weapon', 'A balanced martial sword usable in one or two hands.', 3.0, 'handbook-mentioned', 'main-hand', '1d8', 'slashing', null, null, 0, ['versatile'], 'Melee · 5 ft'],
    ['maul', 'Maul', 'weapon', 'A two-handed hammer capable of delivering crushing blows.', 10.0, 'standard-compatible', 'main-hand', '2d6', 'bludgeoning', null, null, 0, ['heavy', 'two-handed'], 'Melee · 5 ft'],
    ['morningstar', 'Morningstar', 'weapon', 'A spiked martial weapon built to punch through protection.', 4.0, 'standard-compatible', 'main-hand', '1d8', 'piercing', null, null, 0, [], 'Melee · 5 ft'],
    ['pike', 'Pike', 'weapon', 'A very long thrusting polearm for controlling space.', 18.0, 'standard-compatible', 'main-hand', '1d10', 'piercing', null, null, 0, ['heavy', 'reach', 'two-handed'], 'Melee · 10 ft'],
    ['rapier', 'Rapier', 'weapon', 'A slender duelling blade favouring precise technique.', 2.0, 'handbook-mentioned', 'main-hand', '1d8', 'piercing', null, null, 0, ['finesse'], 'Melee · 5 ft'],
    ['scimitar', 'Scimitar', 'weapon', 'A curved blade designed for quick slashing attacks.', 3.0, 'handbook-mentioned', 'main-hand', '1d6', 'slashing', null, null, 0, ['finesse', 'light'], 'Melee · 5 ft'],
    ['shortsword', 'Shortsword', 'weapon', 'A short martial blade suited to quick close-quarters fighting.', 2.0, 'handbook-mentioned', 'main-hand', '1d6', 'piercing', null, null, 0, ['finesse', 'light'], 'Melee · 5 ft'],
    ['trident', 'Trident', 'weapon', 'A three-pronged spear capable of thrusting or throwing.', 4.0, 'standard-compatible', 'main-hand', '1d6', 'piercing', null, null, 0, ['thrown', 'versatile'], 'Thrown · 20/60 ft'],
    ['war-pick', 'War Pick', 'weapon', 'A hardened pick designed to focus force on a narrow point.', 2.0, 'standard-compatible', 'main-hand', '1d8', 'piercing', null, null, 0, [], 'Melee · 5 ft'],
    ['warhammer', 'Warhammer', 'weapon', 'A martial hammer effective in one or two hands.', 2.0, 'standard-compatible', 'main-hand', '1d8', 'bludgeoning', null, null, 0, ['versatile'], 'Melee · 5 ft'],
    ['whip', 'Whip', 'weapon', 'A flexible martial weapon that combines finesse with reach.', 3.0, 'standard-compatible', 'main-hand', '1d4', 'slashing', null, null, 0, ['finesse', 'reach'], 'Melee · 10 ft'],

    // Martial ranged weapons. 'Ranged · 25/100 ft'],
    ['hand-crossbow', 'Hand Crossbow', 'weapon', 'A compact one-handed crossbow for close ranged fighting.', 3.0, 'handbook-mentioned', 'main-hand', '1d6', 'piercing', null, null, 0, ['ammunition', 'light', 'loading', 'ranged'], 'Ranged · 30/120 ft'],
    ['heavy-crossbow', 'Heavy Crossbow', 'weapon', 'A powerful two-handed crossbow with substantial range.', 18.0, 'standard-compatible', 'main-hand', '1d10', 'piercing', null, null, 0, ['ammunition', 'heavy', 'loading', 'ranged', 'two-handed'], 'Ranged · 100/400 ft'],
    ['longbow', 'Longbow', 'weapon', 'A tall bow made for powerful long-distance shots.', 2.0, 'standard-compatible', 'main-hand', '1d8', 'piercing', null, null, 0, ['ammunition', 'heavy', 'ranged', 'two-handed'], 'Ranged · 150/600 ft'],
    ['net', 'Net', 'weapon', 'A weighted net used to hinder a target rather than injure it.', 3.0, 'standard-compatible', 'main-hand', null, null, null, null, 0, ['ranged', 'special', 'thrown'], 'Thrown · 5/15 ft'],

    // Light armour.
    ['padded-armour', 'Padded Armour', 'armour', 'Quilted protective layers that trade silence for basic protection.', 8.0, 'standard-compatible', 'body', null, null, 11, null, 0, ['stealth-disadvantage'], null],
    ['leather-armour', 'Leather Armour', 'armour', 'Supple leather protection suitable for mobile adventurers.', 10.0, 'handbook-mentioned', 'body', null, null, 11, null, 0, [], null],
    ['studded-leather', 'Studded Leather', 'armour', 'Reinforced leather protection with added metal studs and plates.', 13.0, 'standard-compatible', 'body', null, null, 12, null, 0, [], null],

    // Medium armour.
    ['hide-armour', 'Hide Armour', 'armour', 'Thick hides and layered pelts offering rugged protection.', 12.0, 'standard-compatible', 'body', null, null, 12, 2, 0, [], null],
    ['chain-shirt', 'Chain Shirt', 'armour', 'A flexible shirt of linked metal rings worn beneath outer layers.', 20.0, 'standard-compatible', 'body', null, null, 13, 2, 0, [], null],
    ['scale-mail', 'Scale Mail', 'armour', 'Overlapping metal scales fixed to a protective backing.', 45.0, 'standard-compatible', 'body', null, null, 14, 2, 0, ['stealth-disadvantage'], null],
    ['breastplate', 'Breastplate', 'armour', 'A fitted metal torso plate that leaves the limbs relatively free.', 20.0, 'standard-compatible', 'body', null, null, 14, 2, 0, [], null],
    ['half-plate', 'Half Plate', 'armour', 'Substantial plate protection covering the most vulnerable areas.', 40.0, 'standard-compatible', 'body', null, null, 15, 2, 0, ['stealth-disadvantage'], null],

    // Heavy armour.
    ['ring-mail', 'Ring Mail', 'armour', 'Heavy leather reinforced with sewn metal rings.', 40.0, 'standard-compatible', 'body', null, null, 14, 0, 0, ['heavy', 'stealth-disadvantage'], null],
    ['chain-mail', 'Chain Mail', 'armour', 'A full protective coat of interlocking metal rings.', 55.0, 'standard-compatible', 'body', null, null, 16, 0, 0, ['heavy', 'stealth-disadvantage'], null],
    ['splint-armour', 'Splint Armour', 'armour', 'Vertical strips of metal secured over a protective backing.', 60.0, 'standard-compatible', 'body', null, null, 17, 0, 0, ['heavy', 'stealth-disadvantage'], null],
    ['plate-armour', 'Plate Armour', 'armour', 'A fitted suit of shaped metal plates offering exceptional protection.', 65.0, 'standard-compatible', 'body', null, null, 18, 0, 0, ['heavy', 'stealth-disadvantage'], null],

    // Shields.
    ['shield', 'Shield', 'shield', 'A practical defensive shield carried in the off hand.', 6.0, 'handbook-mentioned', 'off-hand', null, null, null, null, 2, [], null],

    // Mundane packs and ammunition named by handbook class equipment.
    ['arrows-20', 'Arrows (20)', 'gear', 'A bundle of twenty arrows for use with bows.', 1.0, 'handbook-mentioned', null, null, null, null, null, 0, ['ammunition'], null],
    ['quiver', 'Quiver', 'gear', 'A carrying case for arrows.', 1.0, 'handbook-mentioned', null, null, null, null, null, 0, [], null],
    ['explorers-pack', 'Explorer’s Pack', 'gear', 'A practical bundle of supplies for travel and exploration.', 59.0, 'handbook-mentioned', null, null, null, null, null, 0, [], null],
    ['burglars-pack', 'Burglar’s Pack', 'gear', 'A collection of practical infiltration and utility supplies.', 47.5, 'handbook-mentioned', null, null, null, null, null, 0, [], null],
    ['dungeoneers-pack', 'Dungeoneer’s Pack', 'gear', 'A bundle of supplies intended for hazardous underground expeditions.', 61.5, 'handbook-mentioned', null, null, null, null, null, 0, [], null],
    ['diplomats-pack', 'Diplomat’s Pack', 'gear', 'Travel and presentation supplies for formal social business.', 36.0, 'handbook-mentioned', null, null, null, null, null, 0, [], null],
    ['entertainers-pack', 'Entertainer’s Pack', 'gear', 'Travel supplies useful to a performer on the road.', 38.0, 'handbook-mentioned', null, null, null, null, null, 0, [], null],
    ['scholars-pack', 'Scholar’s Pack', 'gear', 'Books, writing supplies and practical tools for an itinerant scholar.', 10.0, 'standard-compatible', null, null, null, null, null, 0, [], null],
];
