# Phase III.10.5 — The Indexed Arcane Pantry

The Arcane Pantry now indexes spells and magical/class abilities into accessible shelves rather than presenting one increasingly long list.

## Shelf model

`ArcanePantryPresenter` derives shelves from the existing structured `kind` and `spell_level` fields.

Available shelves are emitted only when they contain entries and are ordered as:

1. Cantrips
2. Level 1
3. Level 2
4. Higher numbered spell levels in ascending order
5. Features

Cantrips remain player-facing **Cantrips**, not “Level 0”. Non-spell class abilities are retained on the **Features** shelf rather than being assigned a fictional spell level.

## Ledger interaction

The casting ability, spell attack, save DC and spell-slot summary remain visible above the index.

The shelf selector uses the ARIA tabs pattern:

- `role="tablist"`
- `role="tab"`
- `role="tabpanel"`
- `aria-selected`
- `aria-controls`
- roving `tabindex`

Keyboard interaction supports Left Arrow, Right Arrow, Home and End. Tabs are horizontally scrollable on narrow screens.

## Diceworks

Spell cards themselves are unchanged. Existing Guild Diceworks triggers for spell attacks, damage and healing remain inside each indexed shelf and retain their character-aware roll context.

## Boundaries

This phase reorganises presentation only. It does not change spell acquisition, spell slots, spell preparation, progression, Diceworks mathematics, or Character persistence.
