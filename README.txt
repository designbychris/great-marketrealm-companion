Phase III.M.3G.1 — The Spellkeeper's Register
Apply the two PHP files to the matching paths in the existing GMRC plugin. Do not replace the plugin with this partial patch.
The Pocket API now resolves explicit legacy spell aliases through the shared register and falls back to existing Arcane Pantry definitions for spells absent from the Handbook register. It preserves the character's recorded spell name where available and does not auto-roll level/slot-scaling spells from a fixed base formula.
The patch does not change the database, spell slots, or the mobile UI. Clear caches, run php vendor/bin/phpunit --display-warnings and verify Market Missile and Shelf Alarm on the Wizard.
