# Phase III.16 — Companion Administration & Security

## Certified incoming baseline
- 3,495 tests
- 11,773 assertions
- all green

## III.16.2 — Gate Security
Cloudflare Turnstile can now be configured from the administrator-only Steward's Office. The Site Key is public; the Secret Key is retained server-side and never rendered back into the admin page. Registration and login protection may be enabled independently. Guild Gate submissions are verified against Cloudflare's Siteverify endpoint before authentication or account creation proceeds.

The configuration deliberately fails open when protection is not enabled/configured, preventing an incomplete Steward configuration from locking administrators out of WordPress. Once enabled with complete credentials, a missing, rejected, or unreachable verification blocks that protected Guild Gate action with a readable error.
