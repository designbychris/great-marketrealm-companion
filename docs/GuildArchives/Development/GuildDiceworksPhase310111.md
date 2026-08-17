# Phase III.10.11.1 — Restocking the Arcane Pantry

Browser testing of a Level 4 Wizard exposed a genuine content shortage in the Arcane Pantry.

The progression system correctly required two new Wizard spells for Level 5, but the character had already learned every eligible Wizard spell in the catalogue. The Spellbook Folio therefore reported a catalogue shortfall and correctly refused Guild Certification.

## The correct fix

The progression rule is unchanged.

A Wizard still:

- learns two spells at each advancement;
- chooses only eligible spells not already in the spellbook;
- may choose spells up to the maximum spell level unlocked by the target level;
- cannot be certified while the catalogue cannot satisfy the required choice count.

The `choose-n` requirement and catalogue-shortfall protection remain authoritative.

## Pantry restock

The Wizard catalogue now contains at least eight spells at each of the first three spell levels.

The restock adds:

### Level 1

- Receipt Shield
- Crate Bolt
- Label Snare
- Stocklight Orb
- Shelf Alarm
- Paper Cut Swarm

alongside Pantry Ward and Market Missile.

### Level 2

- Forklift Hand
- Cold Aisle Shard
- Inventory Mirage
- Barcode Bind

alongside Aisle Step, Stockroom Veil, Price Freeze and Crate Levitation.

### Level 3

- Aisle Lightning
- Stockroom Fireball
- Sealed Delivery
- Closing Time Haste
- Frozen Stock
- Grand Relabel
- Pallet Wall
- Return to Sender

## Level 4 → 5 regression

A regression fixture reproduces the browser-reported state: a Level 4 Wizard who already knows the six original Wizard spells.

When preparing Level 5, the Spellbook Folio must now report:

- two required choices;
- zero catalogue shortfall;
- at least two available choices;
- third-level spell choices available.

This protects the progression gate without weakening it.

## Boundary

This phase is a content restock, not a spellcasting-system rewrite. It does not change spell-slot expenditure, preparation, higher-slot selection, Diceworks scaling, targeting, or Guild Certification rules.
