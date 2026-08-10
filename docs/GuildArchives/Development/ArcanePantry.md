# Character Lifecycle Initiative — Phase III.5: The Arcane Pantry

The Arcane Pantry introduces one reusable spell-and-ability presentation
layer for every supported Character class.

Spellcasting values are derived from the Character:

- spell attack = casting ability modifier + proficiency bonus;
- spell save DC = 8 + casting ability modifier + proficiency bonus.

The initial catalogue includes Great Marketrealm-flavoured cantrips, first
level spells and non-spellcasting class features. Formula rolls reuse the Guild
Dice engine. Damage and healing therefore share the same animated dice path
used by weapon damage.

This phase intentionally establishes the catalogue and mechanical presentation
without yet persisting prepared spell choices or expended spell slots. Those
mutable resources belong naturally with the upcoming progression/resource
work rather than the immutable Character constructor.
