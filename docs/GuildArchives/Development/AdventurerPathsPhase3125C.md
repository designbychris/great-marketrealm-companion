# Phase III.12.5C — The Monk's Discipline Reserves

III.12.5C turns the Discipline Register's read-only capacity into a persistent
active-play resource.

## Reserve rules

- Discipline unlocks at Monk Level 2.
- Maximum Discipline remains owned by `MonkDisciplinePolicy`.
- Active play stores only expended Discipline in the existing
  `ActiveClassResourceState`.
- Spending consumes exactly one point and cannot move below zero.
- A short rest restores all Discipline.
- A long rest restores all Discipline.
- Level changes require no migration: remaining points are reconciled against
  the character's current certified maximum.

## Ledger controls

The Discipline Register now displays `remaining / maximum` and provides:

- Spend 1 Discipline
- Take a Short Rest
- Take a Long Rest

The spend button is disabled when no Discipline remains.

## Boundary

This phase establishes the resource engine only. Specific techniques such as
Stunning Strike will consume this same reserve in III.12.5D.
