GMRC Phase III.M.4A — Pocket Guild Gate & App Foundation

Apply the three files in this archive to the same relative paths in your current plugin. Back up your plugin first and test on staging. No database changes.

Changes:
- Existing unauthenticated Pocket landing page receives app-style copy and full-width sign-in action; still redirects to existing front-end Guild Gate with return_route=pocket.
- Existing Guild Gate gets a Pocket-only visual variant with your bundled high-resolution logo; no new login handler, nonce, anti-bot, or session changes.
- Logged-in Pocket sheet gets responsive forest-green app header and working Home/Character navigation; the existing character sheet, dice, HP, spells and spell-slot controls are retained. The fuller dashboard/feature tabs belong to III.M.4B.
- Two source-level regression tests included; they do not replace live integration/security tests.

Run: php vendor/bin/phpunit --display-warnings
Test signed-out Pocket -> Guild Gate -> signed-in Pocket, refresh/nonces, character selection and back navigation, phone/tablet/desktop widths, spell-slot controls, dice, HP and existing normal GMRC login. Clear any page cache serving signed-out forms; exclude login pages from full-page caching.
