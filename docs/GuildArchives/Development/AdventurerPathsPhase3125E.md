# Phase III.12.5E — The Monk's Final Seal

III.12.5E certifies and hardens the complete Monk implementation.

No new Monk combat mechanic is introduced by the Final Seal.

## Certified Monk stack

The seal protects:

- specialist Monk progression;
- Monastic Way selection at Level 3;
- six Great Marketrealm Monastic Ways;
- Way Gifts at Levels 3 / 6 / 11 / 17;
- identity, playstyle, best-for and gift-preview choice guidance;
- Discipline capacity and save DC;
- Unarmoured Movement scaling;
- persistent Discipline expenditure;
- short-rest and long-rest Discipline recovery;
- level-up reconciliation by storing expenditure rather than maximum;
- Flurry of Blows;
- Patient Defense;
- Step of the Wind;
- Deflect Missiles;
- Return Deflected Missile;
- Slow Fall;
- Stunning Strike;
- shared Guild Diceworks integration;
- application-post route and nonce protection;
- responsive and accessible Ledger presentation.

## Discipline authority

`MonkDisciplinePolicy` remains the authority for permanent values:

- maximum Discipline;
- Discipline Save DC;
- Unarmoured Movement bonus.

`ActiveClassResourceState` stores only expenditure.

This separation means a level-up never needs to rewrite a persisted maximum.

## Technique authority

`MonkDisciplineReserveService` validates named Discipline techniques before
spending the shared reserve.

The Final Seal protects the currently certified costs and gates:

- Flurry of Blows — Level 2, 1 Discipline
- Patient Defense — Level 2, 1 Discipline
- Step of the Wind — Level 2, 1 Discipline
- Return Deflected Missile — Level 3, 1 Discipline
- Stunning Strike — Level 5, 1 Discipline

Unknown techniques, techniques used before their level, and foreign Callings
are rejected.

## Dice-backed boundary

Deflect Missiles is the Monk technique that currently requires a real
Companion roll.

Its reduction remains:

`1d10 + Dexterity modifier + Monk level`

through Guild Diceworks.

The Companion does not infer whether the reaction trigger occurred or whether
a reduced missile qualifies for the return follow-up.

## Non-roll boundary

Patient Defense and Step of the Wind do not invent rolls.

Slow Fall is displayed as:

`5 × Monk level`

and costs no Discipline.

Stunning Strike records the Discipline spend and displays the real Discipline
Save DC, but the Companion does not roll the target's save or claim the
triggering hit qualifies.

## Browser verification completed before seal

The live Monk Ledger has been verified to:

- display the correct Discipline maximum;
- persist named technique spends;
- reduce the remaining Discipline counter after a technique is used.

## Reference implementation status

Once PHPUnit-green, Monk becomes the Companion's reference Calling for a
shared point pool powering multiple distinct named techniques.

Current mature Calling patterns:

- Wizard — spellcasting progression
- Fighter — fixed expendable martial reserves
- Barbarian — persistent combat state plus limited resources
- Rogue — every-turn / once-per-turn / reaction play
- Monk — shared point pool powering multiple techniques
