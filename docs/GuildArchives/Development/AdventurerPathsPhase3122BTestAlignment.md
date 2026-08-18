# Phase III.12.2B — Fighter Martial Paths Test Alignment

The first III.12.2B PHPUnit run produced two regression failures.

## Failure 1 — Level 3 Fighter delegation

The older Fighter Calling regression expected Level 3 to delegate only:

- `path`

III.12.2B intentionally extends the same Level 3 advancement to delegate:

- `path`
- `path-gifts`

This allows the chosen Martial Path's first automatic gift to be discovered
and certified in the same advancement.

The regression now protects both delegated folios and their keys.

## Failure 2 — whitespace-sensitive view assertion

The Martial Register view correctly renders certified path gifts.

The regression searched for the exact contiguous PHP source fragment:

`['path']['gifts']`

The production view formats that nested array access across multiple lines for
readability, so the literal string does not exist even though the behavior is
present.

The regression now checks stable presentation contracts instead:

- the `path` state is referenced;
- the `gifts` state is referenced;
- the view iterates those gifts as `$gift`;
- the existing visible heading and CSS hook assertions remain intact.

## Scope

This alignment changes tests only.

No Fighter progression, Path Gift catalogue, certification, persistence,
Martial Register rendering, CSS, or Character Ledger behavior is changed.
