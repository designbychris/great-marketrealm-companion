# Phase III.12.9A — The Ranger's Field Register

III.12.9A adds the Ranger's read-only Field Register to the Spells & Abilities
Ledger.

## Register contents

The Field Register presents:

- Ranger level;
- Favoured Mark progression stage;
- Natural Explorer fieldcraft identity;
- Extra Attack status;
- Wisdom-based spell save DC;
- Wisdom-based spell attack bonus;
- known-spell half-caster totals;
- highest available spell circle;
- persistent shared spell-slot state;
- next Ranger field milestone;
- current Ranger path catalogue status.

## Favoured Mark boundary

The Arcane Ability Catalogue already contains `Favoured Mark` and describes it
as a limited-use Ranger feature.

The current repository does not define a certified maximum-use formula for
that feature.

III.12.9A therefore displays the feature and its Calling progression stage but
does not invent a spend counter.

A later Ranger active-resource slice can add expenditure once the source rule
is represented in the project.

## Ranger path boundary

The bundled player catalogue currently contains zero Ranger subclasses.

The Register therefore reports:

`Awaiting Ranger path catalogue`

It deliberately does not show an empty or fabricated path selector.

No Ranger `PathProgressionCatalogue` entry is registered by this slice.

## Spellcasting

Ranger spell slots remain on the shared standard spell-slot ledger and are
shown directly in the Field Register.

This keeps the Register and Arcane Pantry on the same persistent active-play
state.

## Accessibility

The Field Register uses an explicit heading relationship, responsive grids and
forced-colours support.

III.12.9A remains read-only and introduces no JavaScript dependency.
