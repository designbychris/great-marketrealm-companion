# Phase III.12.5D — The Monk's Martial Techniques

III.12.5D attaches the persistent Discipline Reserve to real Monk techniques.

## Level 2 techniques

The following techniques cost 1 Discipline:

- Flurry of Blows
- Patient Defense
- Step of the Wind

Each is a server-persisted spend against the shared Discipline Reserve.

No fake dice rolls are created for Patient Defense or Step of the Wind.
Flurry of Blows records the spend while individual attack rolls remain owned
by the normal attack workflow.

## Deflect Missiles

At Level 3, Deflect Missiles becomes an active reaction tool.

The reduction roll is:

`1d10 + Dexterity modifier + Monk level`

The roll reuses Guild Diceworks.

If the table confirms the qualifying missile damage was reduced to zero and
the missile can be returned, the separate **Return Deflected Missile** control
spends 1 Discipline.

The Companion does not infer that the follow-up qualifies.

## Slow Fall

At Level 4, Slow Fall displays its certified reduction:

`5 × Monk level`

It consumes no Discipline and requires no dice roll.

## Stunning Strike

At Level 5, Stunning Strike becomes a 1-Discipline technique.

The Ledger displays the real Discipline Save DC already certified by
`MonkDisciplinePolicy`.

The Companion records the spend but does not roll the target's saving throw or
claim that the triggering hit qualifies.

## Discipline controls

The temporary generic **Spend 1 Discipline** button introduced in III.12.5C
is replaced by named technique controls.

Short Rest and Long Rest remain in the Discipline Register and continue to
restore the same persistent reserve.

## Browser verification

At Level 2:
- Flurry of Blows, Patient Defense and Step of the Wind are available.
- Spending any one reduces Discipline by 1.

At Level 3:
- Deflect Missiles unlocks.
- Roll Reduction opens Guild Diceworks.
- Return Deflected Missile costs 1 Discipline.

At Level 4:
- Slow Fall displays `Reduce 20`.

At Level 5:
- Stunning Strike unlocks.
- Its badge displays the character's real Discipline Save DC.
- Spending it reduces the same shared Discipline pool.
