# Phase III.16.3 — Companion Settings & Steward Diagnostics

## Certified incoming baseline
- 3,500 tests
- 11,800 assertions
- all green

## Purpose
The Steward's Office now acts as a live administrative health ledger rather than a collection of future placeholders. Diagnostics are read-only and classify results as Healthy, Attention, or Informational so an intentionally disabled optional service is not treated as a fault.

## Diagnostic coverage
- PHP runtime baseline;
- WordPress runtime visibility;
- HTTPS request status;
- WordPress media/upload writability;
- outbound WordPress HTTP availability;
- Cloudflare Turnstile configuration;
- registration protection state;
- login protection state.

The Office displays an overall Steward seal and summary counts, with optional environment details for the Companion, WordPress, PHP, site URL, and WordPress memory limit.

## Companion Settings foundation
A dedicated `gmrc_companion_settings` option stores administrator-only operational preferences. The first slice provides a Steward contact email reserved for future service notices plus a preference controlling whether detailed environment values are rendered in diagnostics. Saving requires `manage_options` and a dedicated nonce.

No diagnostic mutates application data, no Turnstile secret is included in diagnostic output, and game mechanics remain outside Companion Settings.

## Next planned slices
- Phase III.16.2A — Guild Gate Tabbed Authentication.
- Canonical Records / Bestiary Stewardship, including WordPress Media Library artwork selection.
