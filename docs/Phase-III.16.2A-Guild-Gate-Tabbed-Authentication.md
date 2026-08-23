# Phase III.16.2A — Guild Gate Tabbed Authentication

## Certified incoming baseline

- 3,507 tests
- 11,832 assertions
- all green

## Purpose

Phase III.16.2A converts the logged-out Guild Gate from parallel login and
registration folios into one accessible tabbed authentication desk without
changing the authentication, role, nonce, or Cloudflare Turnstile contracts.

## Certified slice

- Log In and Join the Guild are exposed as an ARIA tablist.
- Only the active authentication folio is displayed at a time.
- Arrow keys, Home, and End move between tabs with visible focus.
- `?gate=register` and `?gate=login` provide progressive-enhancement deep links.
- Validation failures restore the form that generated the failure through the
  existing flashed `gate_intent` value.
- The current `return_route` survives tab/deep-link navigation.
- History state mirrors the selected tab without forcing a page reload.
- Login and registration keep their independent nonce and Turnstile widgets.
- The tabbed layout retains mobile and forced-colour support.

The phase is intentionally presentation-only. Credential verification,
WordPress authentication, account creation, account-type assignment, and
Turnstile server verification remain owned by the already certified Guild Gate
services.
