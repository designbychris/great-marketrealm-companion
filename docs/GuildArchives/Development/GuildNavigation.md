# Guild Hall Initiative — Phase IV.1: The Guild Navigation

The historic vertical application sidebar is now rendered horizontally across
the top of the Companion.

## Architectural rule

The navigation domain does not change.

`Navigation`, `MenuItem`, route URLs, enabled state and active-route state
continue to work exactly as before. Only the view presentation changes.

## Desktop

The Guild Navigation contains:

- Marketrealm Companion brand;
- horizontal primary navigation;
- current adventurer identity;
- logout utility.

The bar uses Guild cocoa `#402617`, warm cream and a restrained gold active
indicator.

## Tablet and mobile

At 900px and below the primary links collapse behind an accessible `Guild
Menu` button.

The menu:

- reports `aria-expanded`;
- is linked with `aria-controls`;
- closes with Escape;
- returns focus to the menu button after Escape;
- closes after following a link;
- automatically resets when moving back to desktop width.

The navigation is a dropdown, not a modal, so focus is not trapped.

## Layout benefit

Removing the 230–280px permanent sidebar gives illustrated Companion spaces,
especially Auby's Desk and the future Guild Journal and Leather Satchel, the
full viewport width.
