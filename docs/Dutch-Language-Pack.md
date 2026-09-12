# Dutch language pack (`nl_NL`)

The Companion's first production language pack targets **Dutch (Netherlands)** using the WordPress locale `nl_NL`.

Files:

- `languages/great-marketrealm-companion-nl_NL.po` — editable translator catalogue.
- `languages/great-marketrealm-companion-nl_NL.mo` — compiled catalogue loaded by WordPress.

## Scope

This pack translates the interface strings exposed by the current Interface Translation Audit, including character creation controls, Ledger/play controls, the Adventuring Sheet, Printable Sheet, Guild Dice controls, labels, status copy and accessibility text.

Canonical authored MarketRealm content remains separate. Race/class/subclass/spell/item names, rules text and setting lore are not automatically rewritten just because the interface locale is Dutch. Proper names such as Auby, Pippin Peppercorn and MarketRealm remain proper names.

## Adding another locale

Do not add locale branches to PHP or JavaScript. Copy the PO catalogue to the appropriate WordPress locale name, translate `msgstr` values, compile a matching MO file, and keep using the same `great-marketrealm-companion` text domain.
