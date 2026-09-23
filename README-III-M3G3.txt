Phase III.M.3G.3 — The Spellkeeper's Reserves
Apply the four PHP files to their corresponding paths in the existing GMRC plugin. Back up the plugin and character database first; test on staging before production.
This patch introduces explicit standard spell-slot spend/recover on desktop and Pocket; rolls do not spend slots. Warlock Pact Magic is intentionally excluded pending separate resource integration.
The endpoint uses an advisory database lock for requests through this endpoint and an expected-remaining check. Other pre-existing resource writers do not participate in that lock; simultaneous writes by those features require a future shared transactional refactor. Do not describe this as universal concurrency safety.
After upload: clear caches, run php vendor/bin/phpunit --display-warnings and verify desktop/mobile balances and full-rest interactions. A full PHPUnit run and live WordPress tests have not been performed here.
