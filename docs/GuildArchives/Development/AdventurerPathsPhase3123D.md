# Phase III.12.3D — The Barbarian's Primal Actions

III.12.3D connects certified Barbarian features to useful active-play actions
without turning every class feature into an unnecessary dice button.

## Design rule

The Companion now distinguishes between:

- an active Rage state;
- an ability that genuinely rolls dice;
- an ability that modifies another roll;
- an ability that is primarily rules guidance.

This preserves the rules meaning of Barbarian features while reusing the
existing Guild Diceworks where appropriate.

## Rage Damage

The Primal Actions panel reflects the currently persisted Rage state.

While dormant:

`Rage Damage — Dormant`

While Raging it displays the certified bonus:

- Levels 1–8: +2
- Levels 9–15: +3
- Levels 16–20: +4

No separate damage roll is invented because Rage modifies qualifying weapon
damage rather than replacing the weapon's own damage roll.

## Reckless Attack

Reckless Attack becomes a certified action card at Level 2.

It deliberately does not create a generic weapon attack roll because the
Character's real weapons and attack modifiers already belong to the Attacks
folio.

The card tells the player to use the actual weapon attack and select
**Advantage** in Guild Diceworks.

## Danger Sense

Danger Sense receives a real Dexterity saving-throw action at Level 2.

The roll uses:

- the Character's real Dexterity saving-throw modifier;
- the Character's real Dexterity save proficiency state;
- Guild Diceworks;
- Advantage as the contextual default mode.

The player can still choose another roll mode if the table situation requires
it.

## Contextual Diceworks mode

Guild Diceworks now accepts an optional:

`data-roll-default-mode`

with one of:

- normal
- advantage
- disadvantage

Opening that roll focuses the matching Diceworks mode button.

Existing roll buttons without this attribute continue to default to Normal.

This is shared infrastructure for later class mechanics.

## Brutal Critical

Brutal Critical stays guidance rather than becoming a fake standalone damage
roll.

The card scales with certified Barbarian level:

- Level 9: +1 weapon die
- Level 13: +2 weapon dice
- Level 17: +3 weapon dice

The normal weapon critical is still resolved through the existing attack and
critical-damage flow.

## Relentless Rage

At Level 11, Relentless Rage receives a Constitution saving-throw action.

It uses the Character's real Constitution save modifier and proficiency.

The action is only available while the persisted Rage state is active.

The result reminds the player that the first Relentless Rage DC is 10 and that
successful repeated uses become harder until a rest.

III.12.3D deliberately does not invent a second DC-tracking subsystem; the
roll remains honest about the current rule boundary.

## Indomitable Might

At Level 18, Indomitable Might receives a Strength check.

The roll uses the Character's real Strength modifier.

The action card and result identify the Character's raw Strength score as the
minimum result floor that Indomitable Might can apply.

## Rage Register integration

The existing Rage Register now includes a **Primal Actions** section.

Locked abilities remain visible and subdued.

Dice-backed actions are disabled when their current state requirement is not
met, such as Relentless Rage while Rage is dormant.

## No extra persistence

III.12.3D introduces no new Character persistence.

The actions consume:

- certified Character progression;
- existing saving throws and ability scores;
- III.12.3C's persisted Rage condition.

## Browser test

For the current Level 3 Barbarian:

1. Confirm Rage Damage is Dormant before Rage begins.
2. Enter Rage.
3. Confirm Rage Damage becomes +2.
4. Confirm Reckless Attack is certified.
5. Open Danger Sense.
6. Guild Diceworks should focus the **Advantage** roll mode.
7. Confirm the roll uses the Character's Dexterity saving-throw modifier.

At Level 9:

- Brutal Critical should show +1 die.

At Level 11 while dormant:

- Relentless Rage is certified but its roll button is unavailable.

Enter Rage:

- Relentless Rage becomes available.
- Its roll uses the Character's Constitution saving-throw modifier.

At Level 18:

- Indomitable Might shows the real Strength score as its minimum check result.
