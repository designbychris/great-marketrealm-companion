# Character Lifecycle Initiative — Phase III.7.3.3
## Project Golden Apple — The Guild Wardrobe

The portrait system now has a reusable class wardrobe for every current
handbook class.

### Coverage

13 current classes are covered:

Artificer, Barbarian, Bard, Cleric, Druid, Fighter, Monk, Paladin, Ranger, Rogue, Sorcerer, Warlock, Wizard.

Each class receives:

- two outfit variants;
- two equipment variants;
- one optional accessory plus None;
- one optional class aura/effect plus None;
- one optional Guild ornament plus None.

That produces 91 new vector wardrobe assets.

### Independence contract

Race/heritage determines the creature. Class determines what that creature
wears, carries and radiates.

No wardrobe asset contains a race identifier, so a Boxfolk Fighter and a
Fructan Fighter can share Fighter armour/equipment without sharing anatomy.

### Workbench

The Creator and Private Studio can now adjust:

- Outfit
- Equipment
- Accessory
- Class aura
- Guild ornament
- Ambient effects
- Frame

Class aura and Guild ornament are now true persisted portrait recipe slots.

### Legacy compatibility

Grocer and Cleaver Saint remain supported by the old asset fallback for
existing saved characters, but are not added back to the current handbook
catalogue.
