# Phase III.16.20 — The Steward's Folk & Heritage Workshop

Phase III.16.20 closes the remaining Character-identity authoring gap by adding Steward-authored playable Folk and Heritages to the existing Workshop lifecycle.

## Lifecycle and identity

Canonical Folk remain protected. Steward records use stable `steward-folk-*` identities and may be Draft, Published, or Archived. Published records join new Character inscription; archived records are removed from new selection while remaining resolvable for existing Characters that already carry the stable Folk identity.

Heritages use stable `steward-heritage-*` keys tied to their parent Folk. Only Heritages belonging to a Published parent are projected into the Character Catalogue.

## Certified mechanics boundary

The Workshop records name, description, size, creature type, walking speed, darkvision, languages, descriptive traits and Heritage reference material. Publication requires a complete description, recognised size, creature type and a walking speed in supported five-foot increments.

These fields do not invent new executable racial powers. Bespoke racial features remain descriptive until the Companion has an explicit structured mechanics bridge for them.

## Portrait safety

Steward Folk do not require bespoke portrait assets in order to become playable. The portrait system already tolerates a race family with no dedicated race layers and continues using its shared safe layers. Dedicated Folk/Heritage art may therefore be added later without changing the stored Character identity.

## Destructive safety

Permanent deletion is dependency-guarded. A Steward Folk cannot be deleted while any Character references its Folk key or one of its Heritage keys. Archive remains the normal retirement mechanism.

## Workshop certification

The Steward's Office certification projection expands from five to six authoring rooms: Monsters, Spells, Backgrounds, Equipment, Callings & Paths, and Folk & Heritages.
