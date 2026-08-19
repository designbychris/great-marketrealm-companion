# Phase III.12.4D — The Rogue's Precision & Reactions

III.12.4D turns the Rogue's precision and defensive reaction features into
active-play Ledger tools without making battlefield decisions for the player.

## Sneak Attack

Sneak Attack now has a live precision-damage control using the certified Rogue
scaling already present in the Cunning Register:

`1d6 → 2d6 → ... → 10d6`

The damage roll reuses Guild Diceworks.

The Companion deliberately does not decide whether an attack qualifies.
Instead it provides qualification guidance and lets the player mark Sneak
Attack used after applying it to a qualifying hit.

Because Sneak Attack is **once per turn**, not a rest-based resource, its
used/ready state is browser-local and resets with **Start New Turn**.

## Uncanny Dodge

At Rogue Level 5, **Declare Uncanny Dodge** becomes available.

This is a reaction declaration, not a dice roll and not a rest-based reserve.
The Companion marks the reaction declaration for the current play cycle while
leaving visibility, attack qualification and final damage resolution to the
table.

**Start New Turn** resets the local reaction record.

## Evasion

At Rogue Level 7, Evasion is displayed as certified passive resolution
guidance.

It receives no use button or counter because it is not a spendable resource.

## Safety against invented rules

The Companion does not infer:

- whether Sneak Attack qualifies;
- whether the attacker is visible;
- whether Uncanny Dodge can legally trigger;
- whether an effect qualifies for Evasion;
- final post-reduction damage.

Those remain scene/table decisions.

## Accessibility

The active-play panel uses native buttons, `aria-pressed` state where
appropriate, a polite live region, visible keyboard focus and responsive /
forced-colours presentation.

## Browser verification

At Rogue Level 3:
1. Roll Sneak Attack and confirm Diceworks uses `2d6`.
2. Mark Sneak Attack used.
3. Confirm the roll/use controls become unavailable for the current turn.
4. Press Start New Turn and confirm Sneak Attack becomes ready again.
5. Confirm Uncanny Dodge and Evasion remain locked.

At Rogue Level 5:
1. Confirm Uncanny Dodge unlocks.
2. Declare it and confirm the reaction declaration is announced.
3. Start New Turn and confirm it becomes available again.

At Rogue Level 7:
1. Confirm Sneak Attack is `4d6`.
2. Confirm Evasion appears as passive guidance with no use counter.
