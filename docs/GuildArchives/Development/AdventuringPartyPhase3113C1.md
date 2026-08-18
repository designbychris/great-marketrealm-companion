# Phase III.11.3C.1 — Ledger Layout Repair

A browser review during III.11.3C showed the WordPress theme footer appearing
too visually close to the active Character Ledger book.

## Diagnosis

The Character `show.php` block markup was checked directly.

The following element families remain balanced:

- section;
- div;
- article;
- aside;
- form;
- nav;
- header.

The Adventurer's Purse forms are also outside any pre-existing form boundary,
so this was not caused by invalid nested forms.

Fellowship CSS was checked for Character Ledger leakage and does not target
`.gmrc-open-ledger`.

The visual problem came from the Ledger having no explicit terminal layout
boundary of its own. Because inactive Ledger tabs are hidden, the active book
can finish close to the surrounding WordPress theme footer and visually make
the two surfaces appear connected.

## Repair

The Character Ledger root now carries:

`data-character-ledger-boundary`

The Open Ledger establishes its own terminal layout boundary with:

- explicit bottom breathing room;
- `clear: both`;
- layout isolation;
- clearfix protection against surrounding theme/plugin floats;
- responsive spacing.

This is deliberately applied to the Character Ledger itself rather than
changing the global WordPress footer or Fellowship Hall.

## Regression protection

The repair includes coverage that verifies:

- the Character Ledger boundary remains present;
- bottom separation remains defined;
- the Character markup remains block-balanced;
- purse forms remain outside other forms;
- Fellowship CSS does not target the Character Ledger boundary;
- responsive handling remains present.

III.11.3C transfer behaviour is unchanged by this repair.
