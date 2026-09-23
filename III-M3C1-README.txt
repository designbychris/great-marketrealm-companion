Phase III.M.3C.1 — Pocket Vitality Controls

Apply app/Mobile/PocketPage.php over the deployed III.M.3C file. Add tests/Unit/Mobile/PocketVitalityControlsRegressionTest.php.

Adds damage and healing buttons, temporary HP absorption, max-HP healing cap, input validation, disabled controls during save, stale-data message, and ledger redraw after returning from a sheet. Reuses the existing authenticated vitality POST endpoint; no database or API schema changes.

Tests added are source-contract regression checks for the browser UI, NOT REST integration or concurrency tests. The current API checks expected HP before save but does not guarantee an atomic compare-and-swap across concurrent requests. Verify the existing REST endpoint's ownership, authentication, validation and conflict behaviour in a WordPress integration environment before claiming full security/concurrency coverage.

Run php vendor/bin/phpunit --display-warnings and test on staging first. On a test character, verify damage absorbs temporary HP before current HP, healing stops at maximum, and the main Companion reflects saved changes. Refresh characters after a conflict.
