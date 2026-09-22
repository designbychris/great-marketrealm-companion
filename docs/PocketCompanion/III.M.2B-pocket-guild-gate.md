# III.M.2B — Pocket Guild Gate

Browser-only phase: a branded Pocket entry sends guests to the existing front-end Guild Gate, retaining its nonce, rate/bot protections and existing WordPress authentication. A constrained `return_route=pocket` returns successful sign-ins to the published page containing `[gmrc_pocket_companion]`. No new password handler, mobile tokens, or native authentication is introduced.

Create/publish the Pocket Companion page before testing. Test unauthenticated entry, successful login, failed login, configured Turnstile, and return to ledger on Pixel and iPhone. Existing REST endpoints remain owner-scoped.

The provided original logo is packaged locally. Approved illustration is NOT included: supply a clean artwork-only background before introducing it; do not use a generated mockup containing fake form fields as a live background.

Run `php vendor/bin/phpunit --display-warnings`; add dedicated redirect/ownership integration coverage in the next certification pass.
