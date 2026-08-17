# Phase III.10.11.1a — Pantry Restock Regression Fix

The Arcane Pantry restock exposed two assumptions that were safe only while the catalogue was small.

## Live Pantry spell-circle gate

`ArcanePantryPresenter` previously filtered entries by minimum character level but did not cap numbered spells by the highest spell slot the character can currently cast.

Once Level 3 spells were added to the catalogue, a Level 4 Wizard could therefore see a Level 3 shelf before Level 5.

The presenter now derives the adventurer's current maximum spell level from the existing slot table and only exposes numbered spells at or below that level.

Cantrips and class features remain governed by their existing availability rules.

## Level 3 advancement regression

The old Level 3 Wizard regression expected exactly four available spell choices because the original catalogue contained only four unlearned eligible spells.

After the restock, a Wizard may correctly choose from additional unlearned Level 1 and Level 2 spells. The regression now protects the actual rule instead:

- two spell choices are still required;
- at least four eligible choices exist;
- the original Level 2 studies remain available;
- the catalogue may safely grow beyond the original size.

The Wizard `choose-n` progression requirement remains unchanged.
