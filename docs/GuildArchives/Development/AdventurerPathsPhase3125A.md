# Phase III.12.5A — The Monk's Discipline Register

III.12.5A adds a read-only, level-aware Monk field record to the Character
Ledger.

The register presents the Monk's current Discipline capacity, Discipline save
DC, Unarmoured Movement bonus, major feature unlocks, Monastic Way status and
next major milestone.

No Discipline is spent or persisted in this phase. Active resource mutation is
reserved for III.12.5C.

## Discipline policy

The shared `MonkDisciplinePolicy` establishes one authority for later Monk
phases:

- Discipline unlocks at Level 2.
- Maximum Discipline equals Monk level from Level 2 onward.
- Discipline save DC = 8 + proficiency bonus + Wisdom modifier.
- Unarmoured Movement bonus progresses from +10 ft at Level 2 through +30 ft
  at Level 18.

## Browser checks

Level 1:
- Discipline 0
- movement bonus 0
- Monastic Way opens at Level 3

Level 2:
- Discipline 2
- movement +10 ft

Level 3:
- Discipline 3
- Monastic Way available

Level 5:
- Discipline 5
- Stunning Strike certified
