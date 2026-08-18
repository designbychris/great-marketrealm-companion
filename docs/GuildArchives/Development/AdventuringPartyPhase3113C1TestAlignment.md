# Phase III.11.3C.1 — Ledger Layout Repair Test Alignment

The first III.11.3C.1 PHPUnit run exposed one regression assertion that was
too broad for a PHP template.

## Failure

The regression attempted to prove that the Adventurer's Purse forms were not
nested by counting every opening and closing `<form>` tag appearing earlier
in the raw `show.php` source.

That assumption is not reliable for a conditional PHP template because forms
from other Ledger workflows may begin and end across conditional source
regions without representing an invalid nested form in the rendered DOM.

## Correction

The regression now inspects the Adventurer's Purse section itself and verifies
that its form markup is self-contained and balanced locally.

This preserves the intended protection without making assumptions about
unrelated forms elsewhere in the Character Ledger source.

No production Character, Purse, Fellowship, CSS, or transfer behaviour changes
are introduced by this alignment.
