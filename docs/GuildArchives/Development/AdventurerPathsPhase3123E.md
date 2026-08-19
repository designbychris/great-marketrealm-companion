# Phase III.12.3E — The Barbarian's Final Seal

III.12.3E hardens the complete Barbarian implementation and certifies it as
GMRC's active-state/resource reference Calling.

No new Barbarian feature is introduced by this phase.

## Certified stack

The Final Seal protects the full Barbarian implementation built through
III.12.3–III.12.3D:

- specialist Barbarian progression;
- Level 3 Primal Path certification;
- eight Primal Paths;
- automatic Path Gifts at Levels 3 / 6 / 10 / 14;
- richer pre-choice Path guidance;
- Rage Register;
- persistent Rage expenditure;
- persistent active Rage state;
- Enter Rage / End Rage / Long Rest;
- Level 20 unlimited Rage;
- Primal Actions;
- Danger Sense Diceworks advantage;
- Relentless Rage state gating;
- Indomitable Might check guidance;
- responsive and accessible presentation.

## Path of the Butchered Rage benchmark

The live benchmark remains:

- Level 3 — Bloodied Cleaver
- Level 6 — Butcher's Instinct
- Level 10 — Carving Frenzy
- Level 14 — Slaughterhouse Fury

Only persisted, Guild-Certified gifts appear in the Rage Register.

## Rage resource boundary

Certified level determines maximum Rage capacity.

Active resource persistence stores expenditure only.

This means a Barbarian can level from a lower Rage maximum to a higher one
without migrating stored maximum values.

Example:

- Level 3 maximum: 3
- 2 Rages already expended
- Level advances to 6
- new maximum: 4
- remaining: 2

## Active Rage boundary

Active Rage is stored separately from expenditure in the reusable active class
condition layer.

Ending Rage does not refund the spent use.

Long Rest restores Rage expenditure and ends the active condition.

Level 20 enters Rage without creating finite expenditure.

## Rage damage single authority

Earlier slices derived Rage damage in both the Register and Primal Action
presenters.

The Final Seal removes that duplication.

`BarbarianRageReserveService::damageBonus()` is now the single certified
Rage-damage scaling authority used by both surfaces:

- Levels 1–8: +2
- Levels 9–15: +3
- Levels 16–20: +4

This is a behavior-neutral cleanup.

## Primal Actions

The Final Seal protects the established action boundaries:

- Rage Damage reflects the persisted Rage state;
- Reckless Attack remains guidance and reuses the actual weapon attack;
- Danger Sense uses the real Dexterity save and defaults Diceworks to
  Advantage;
- Brutal Critical remains weapon-critical guidance rather than a fake damage
  roll;
- Relentless Rage uses the real Constitution save and requires active Rage;
- Indomitable Might uses the real Strength modifier and advertises the raw
  Strength score floor.

## Security and ownership

Rage mutation remains POST-only.

Enter / End / Rest commands share the dedicated nonce contract:

`gmrc_character_rage_{characterId}`

Active resource and condition repositories remain owner-scoped through the
current WordPress user and `_gmrc_character_id`.

## Class isolation

Barbarian-specific presentation and active-play policy do not leak to Fighter
or other Callings.

Non-Barbarians:

- do not receive a Rage Register;
- do not receive Primal Actions;
- cannot use Barbarian Rage Reserve policy.

## Presentation seal

The final surface retains:

- labelled Rage Register;
- labelled Rage Reserves controls;
- labelled Primal Actions;
- responsive single-column fallbacks;
- reduced-motion handling for active Rage;
- forced-colours support.

## Reference implementation status

Once this phase is PHPUnit-green, Barbarian becomes GMRC's reference
implementation for a Calling with both:

1. a limited resource pool; and
2. a persistent active state.

Together the three mature reference Callings now demonstrate different
architectural families:

- Wizard — spellcasting progression;
- Fighter — martial resources and action contracts;
- Barbarian — persistent active state plus limited resources.

Future Callings can reuse those proven primitives without copying their rules.
