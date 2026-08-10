# Character Lifecycle Initiative — Phase III.7.2.1: The Spacious Register

The Adventurer Register previously used an auto-fit grid which could place
four horizontal ledger cards across a wide desktop viewport. This squeezed
identity content and caused short character names to wrap unnaturally.

This pass:

- limits the Register to two columns on desktop;
- switches to one column at 1100px and below;
- preserves the existing stacked mobile-card treatment;
- gives the identity/content side of each record more room;
- uses natural wrapping for names;
- leaves the "Inscribe a New Adventurer" prompt in the same grid flow.

No Character, catalogue, portrait, dice, progression or persistence logic
is changed.
