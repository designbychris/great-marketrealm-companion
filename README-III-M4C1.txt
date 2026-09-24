Phase III.M.4C.1 — Pocket Navigation Dock
Apply the two files in this ZIP over the same relative paths in your current GMRC plugin. Back up first and test on staging.
This patch uses five primary navigation buttons: Overview (Home), Character, Combat, Spellbook (Spells), and More. Equipment is in More. The existing single shared Diceworks tray remains above the mobile navigation.
Run php vendor/bin/phpunit --display-warnings and test phone, desktop, keyboard, and safe-area layouts. No database changes.
