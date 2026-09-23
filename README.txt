Phase III.M.3G.2 — The Spellkeeper's Measures
Apply these three PHP files to matching paths in the latest GMRC repository.
Scope: read-only canonical spellcasting ability, attack bonus, save DC, and owner-scoped shared spell-slot balances. Spell attack quick-roll uses existing Guild Diceworks. No slot editing, spending, or recovery is added.
Run php vendor/bin/phpunit --display-warnings on your server; then verify Wizard and Artificer against desktop and test slot balances after a desktop expenditure.
