# Character Lifecycle Initiative — Phase III.2: The Guild Dice

The Guild Dice turns the Open Ledger into an at-the-table play surface.

## First supported rolls

- Ability Checks;
- Saving Throws;
- Skills;
- Initiative.

Each Ledger value uses the reusable `guild-roll-trigger` component. The component
contains no rule-specific JavaScript, so future attacks, spell attacks, weapon
damage and other equipment rolls can reuse the same engine.

## Roll modes

The Guild Dice tray offers Normal, Advantage and Disadvantage. Advantage rolls
two d20s and keeps the higher natural result; Disadvantage keeps the lower.

The browser uses `crypto.getRandomValues()` with rejection sampling when Web
Crypto is available, and a Math.random fallback for older environments.

## Accessibility

Roll triggers are native buttons. The tray is keyboard operable, closes with
Escape, and announces completed arithmetic through an `aria-live` region.
Animations are disabled under `prefers-reduced-motion`.

## Ledger tabs

The Living Ledger's existing ARIA tabs remain unchanged semantically. Phase
III.2 gives them cocoa hanging-box styling along the lower edge of the right
page, with a gold icon treatment and a purple active state. The component
structure leaves room for future Attacks, Spells and Equipment pages.
