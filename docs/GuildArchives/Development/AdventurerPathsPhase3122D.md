# Phase III.12.2D — The Fighter's Martial Actions

III.12.2D connects certified Fighter abilities to active Character Ledger
actions while preserving the Battle Reserve system introduced in III.12.2C.

## Design rule

Not every class ability needs dice.

The Companion now distinguishes between:

- a resource that is spent;
- an action that rolls dice;
- a resource whose action does not roll;
- a reroll that must use the Character's real modifier.

This prevents the Fighter from becoming a collection of decorative dice
buttons that do not match the underlying ability.

## FighterMartialActionPresenter

The new `FighterMartialActionPresenter` derives action contracts from the
certified Fighter Character.

It does not persist anything.

Battle Reserve expenditure remains owned by:

- `ActiveClassResourceState`;
- `ActiveClassResourceRepository`;
- `FighterBattleReserveService`.

The Martial Action presenter only tells the Ledger how a certified ability can
be used.

## Second Wind

Second Wind now exposes a real Guild Diceworks healing roll:

- formula: `1d10`;
- modifier: certified Fighter level;
- result suffix: `HP recovered`.

Examples:

- Level 1: `1d10 + 1`
- Level 4: `1d10 + 4`
- Level 17: `1d10 + 17`

The Diceworks roll and the Battle Reserve remain intentionally separate.

The player rolls Second Wind and then marks the reserve spent.

The expenditure button therefore reads:

**Mark Second Wind Spent**

rather than ambiguously duplicating the roll action.

Once no Second Wind remains, its roll action is disabled until the appropriate
rest restores the reserve.

## Action Surge

Action Surge deliberately receives no dice roll.

Its action contract explains that the Fighter spends one reserve and takes one
additional action that turn.

Its Battle Reserve button reads:

**Use Action Surge**

This is the action itself.

## Indomitable

Indomitable must reroll the saving throw that actually failed.

The Martial Register therefore derives six reroll options from the Character's
real saving throw collection:

- STR
- DEX
- CON
- INT
- WIS
- CHA

Each reroll uses:

- `d20`;
- the actual current saving throw modifier;
- the Character's real proficiency status;
- Guild Diceworks' existing saving-throw roll contract.

This means future ability-score or proficiency changes automatically flow into
Indomitable without a duplicate Fighter modifier table.

As with Second Wind, the player rerolls and then marks the reserve spent.

The expenditure button reads:

**Mark Indomitable Spent**

## Battle Reserve integration

The existing III.12.2C remaining-use state remains authoritative.

At zero remaining uses:

- dice-backed martial actions are disabled;
- the expenditure control reads `Reserve Spent`;
- the existing short/long-rest lifecycle restores the appropriate abilities.

## Reusable direction

This phase introduces an important pattern for later classes.

A class resource can now have a separate action contract.

Future examples can include:

- Barbarian Rage — no roll, activate state;
- Bardic-style inspiration — die roll tied to resource expenditure;
- Paladin healing pools — amount/application action;
- Monk techniques — resource spend plus attack/save/utility action;
- Path Gifts — optional action contracts when a gift becomes mechanically
  interactive.

The shared Guild Diceworks remains the roll engine instead of each class
inventing its own JavaScript dice system.

## Browser test

For the current Fighter:

### Second Wind

1. Confirm Second Wind has a roll button.
2. Roll it.
3. Guild Diceworks should show `1d10 + Fighter level`.
4. Click **Mark Second Wind Spent**.
5. Remaining should become `0 / 1`.
6. The roll button should be unavailable until a short or long rest.

### Action Surge

1. Click **Use Action Surge**.
2. Remaining becomes `0 / 1`.
3. No pointless dice roll is generated.
4. Take a short rest and confirm it returns to `1 / 1`.

### Indomitable at Level 9+

1. Spend/fail a saving throw in play.
2. Choose the matching STR/DEX/CON/INT/WIS/CHA reroll.
3. Guild Diceworks uses that Character's real save modifier.
4. Mark Indomitable spent.
5. A short rest does not restore it.
6. A long rest does.
