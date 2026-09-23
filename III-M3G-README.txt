Phase III.M.3G — Pocket Spellbook (conservative first delivery)

Apply the three PHP files at their original relative paths to your current GMRC installation.
The Pocket API exposes the current character's learned cantrips and spells, resolving metadata through the SharedSpellRegister. Unknown IDs remain visible without invented mechanics.
The mobile sheet shows a read-only collapsible spellbook. Only explicitly tagged damage/healing with a simple NdX formula and no casting modifier gets an automatic shared-Diceworks roll. All other spell mechanics remain manual.
Spellcasting ability, spell attack bonus, save DC, slot counts and slot consumption are intentionally deferred: this patch does not infer or mutate them. No database changes.
Run php vendor/bin/phpunit --display-warnings and check an actual spellcasting character on mobile.
