# Phase III.12.6 Enhancement — Future Path Preview

This enhancement turns the Character Creator's **Future path / subclass**
selector into an informed choice rather than a name-only dropdown.

## Shared source of truth

`SubclassPreviewCatalogue` combines the existing Grand Catalogue subclass
records with:

- `PathChoiceGuideCatalogue`
- `PathGiftCatalogue`

This means creation-time previews and later advancement choices do not maintain
separate subclass descriptions.

When a Calling later receives new certified Path Gifts, the Character Creator
can surface those gifts automatically.

## Preview content

After a subclass is selected, the creator may show:

- specialist identity / description;
- **How it plays** guidance;
- **Best for** guidance;
- up to four future certified gifts.

If the path has not reached its gift-certification phase yet, the preview says
future specialist gifts will appear as progression becomes Guild Certified.
It does not invent mechanical gifts.

Choosing **Choose later / no subclass yet** hides the preview again.

Changing the top-level class also clears or updates the preview through the
existing Grand Catalogue filtering behavior.

## Paladin enhancement

The three currently registered Paladin Oaths receive non-mechanical choice
guidance immediately:

- Oath of Inventory
- Oath of the Colonel
- Oath of the Creamfather

Their mechanical gift progression remains deliberately deferred to
III.12.6B — The Paladin's Sacred Oaths.

## Accessibility

The preview region uses `aria-live="polite"` and `aria-atomic="true"` so the
new description is announced when a player changes the selector.

The interface remains keyboard-native because the controlling element is the
existing HTML select.

Responsive and forced-colours presentation is included.

## Framework audit alignment

Paladin becoming a specialist Calling raises the framework audit from five to
six specialist Callings and reduces remaining foundation Callings by one.
The enhancement push aligns those stale expectations without changing
production progression behavior.
