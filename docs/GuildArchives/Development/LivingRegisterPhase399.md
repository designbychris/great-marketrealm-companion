# Phase III.9.9 — The Registrar's Final Audit Disclosure

Phase III.9 closes with a presentation-only refinement to the Character Ledger's Registrar's Final Audit.

## Behaviour

- A `Hide Audit` / `Show Audit` button sits beside the folio-readiness pill.
- The audit header, title, summary and folio-readiness count remain visible when collapsed.
- The Adventurer's Seal and folio cards are grouped inside the disclosure region.
- The control uses `aria-expanded` and `aria-controls`.
- Keyboard focus receives a strong visible focus treatment.
- The collapsed preference is stored per character with a local browser key (`gmrc-audit-collapsed-{characterId}`).
- Storage failures are caught safely, so privacy-restricted browsers still receive a functioning in-page disclosure.

## Boundary

This preference is presentation state only. It does not alter the Character aggregate, certification history, progression, or Guild records.

With this disclosure in place, Phase III.9 — The Living Register is ready to be formally sealed before Phase III.10 — The Guild Diceworks.
